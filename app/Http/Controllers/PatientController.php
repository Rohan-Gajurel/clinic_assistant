<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\LabOrder;
use App\Models\ObservationExamination;
use App\Models\Diagnosis;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    //
    public function index()
    {
        $patients = Patient::with('user')->get();
        return view('backend.patients.patients', compact('patients'));
    }

    public function create()
    {
        // $users = User::whereDoesntHave('patient')->get();
        $users= User::all();
        return view('backend.patients.create_patient', compact('users'));
    }

    public function store(Request $request)
    {
    
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string|max:10',
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'age_unit' => 'required|in:years,months,days',
            'date_of_birth' => 'nullable|date',
            'sex' => 'required|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'contact_number' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'id_card_type' => 'nullable|in:passport,citizenship,driver_license,national_id',
            'id_card_number' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'patient_type' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'local_level' => 'nullable|string|max:255',
            'ward_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('patients', 'public');
        }

        $patient = Patient::create($data);
        return redirect()->route('appointments.create',  $patient->id)->with('success', 'Patient created successfully. You can now book an appointment for this patient.');
    }

    public function edit($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return view('backend.patients.edit_patient', compact('patient'));
    }

    

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'nullable|string|max:10',
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'age_unit' => 'required|in:years,months,days',
            'date_of_birth' => 'nullable|date',
            'sex' => 'required|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'contact_number' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'id_card_type' => 'nullable|in:passport,citizenship,driver_license,national_id',
            'id_card_number' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'patient_type' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'local_level' => 'nullable|string|max:255',
            'ward_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

            if ($request->hasFile('photo')) {
                if ($patient->photo) {
                    Storage::disk('public')->delete($patient->photo);
                }
                $data['photo'] = $request->file('photo')->store('patients', 'public');
            }

        $patient->update($data);
        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }     
    
    public function visitDetails(Request $request, $id)
    {
        $patient = Patient::with(['observationVitals', 'diseaseHistory', 'drugHistory'])->findOrFail($id);
        
        if($patient->appointments->isEmpty()){
            return redirect()->route('appointments.create', $patient->id)->with('info', 'Visit details are only available for patients with upcoming or ongoing appointments.');
        }

        // Get specific appointment if appointment_id is provided
        $appointmentId = $request->query('appointment_id');
        
        if ($appointmentId) {
            // Show specific appointment (from visit history)
            $currentAppointment = $patient->appointments()->with('doctor.user')->find($appointmentId);
        } else {
            // Show latest non-completed appointment or the most recent one
            $currentAppointment = $patient->appointments()
                ->with('doctor.user')
                ->where('status', '!=', 'completed')
                ->orderBy('appointment_date', 'desc')
                ->first();
            
            // If no active appointment, get the latest one
            if (!$currentAppointment) {
                $currentAppointment = $patient->appointments()
                    ->with('doctor.user')
                    ->orderBy('appointment_date', 'desc')
                    ->first();
            }
        }

        // Fetch vitals
        $vitals = $patient->observationVitals;

        // Fetch examination data
        $examination = ObservationExamination::where('patient_id', $patient->id)->latest()->first();

        // Fetch disease and drug history
        $diseaseHistories = $patient->diseaseHistory;
        $drugHistories = $patient->drugHistory;

        // Fetch diagnoses for current appointment
        $diagnoses = Diagnosis::where('appointment_id', $currentAppointment->id)->get();

        // Fetch medications for current appointment
        $medications = Medication::where('appointment_id', $currentAppointment->id)->get();

        // Fetch lab orders with service (test or group)
        $labOrders = LabOrder::where('appointment_id', $currentAppointment->id)
            ->with(['service', 'labResults.labTest'])
            ->get();

        // Fetch dispatched lab results from bills for this appointment
        $dispatchedResults = Bill::where('appointment_id', $currentAppointment->id)
            ->with(['dispatchedItems.service', 'dispatchedItems.labResults.labTest'])
            ->get()
            ->pluck('dispatchedItems')
            ->flatten();
        
        // Load tests for LabGroup services in dispatched items
        foreach ($dispatchedResults as $item) {
            if ($item->isLabGroup() && $item->service) {
                $item->service->load('tests');
            }
        }

        return view('backend.patients.visit_details', compact(
            'patient', 
            'currentAppointment', 
            'labOrders',
            'dispatchedResults',
            'vitals',
            'examination',
            'diseaseHistories',
            'drugHistories',
            'diagnoses',
            'medications'
        ));
    }

}
