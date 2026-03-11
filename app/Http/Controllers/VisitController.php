<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\DiseaseHistory;
use App\Models\Diagnosis;
use App\Models\Medication;
use App\Models\LabOrder;
use App\Models\ObservationExamination;
use App\Models\ObservationVital;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    //

    public function storeExamination(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'primary_symptom' => 'nullable|string',
            'other_symptoms' => 'nullable|string',
            'symptom_duration_value' => 'nullable|integer',
            'symptom_duration_unit' => 'nullable|string',
        ]);

        ObservationExamination::create($data);
        return redirect()->back()->with('success', 'Examination details saved successfully.');
    }

    public function upsertVitals(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|string',
            'temperature' => 'nullable|string',
            'respiratory_rate' => 'nullable|string',
            'weight' => 'nullable|string',
            'height' => 'nullable|string',
        ]);

        $vital = ObservationVital::where('patient_id', $request->patient_id)->first();

        if ($vital) {
            // Update existing vital signs
            $vital->update($data);
            return redirect()->back()->with('success', 'Vital signs updated successfully.');
        } else {
            // Create new vital signs
            ObservationVital::create($data);
            return redirect()->back()->with('success', 'Vital signs created successfully.');
        }
    }

    public function storeDiseaseHistory(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'name' => 'nullable|array',
            'name.*' => 'nullable|string',
            'duration_value' => 'nullable|array',
            'duration_value.*' => 'nullable|string',
            'duration_unit' => 'nullable|array',
            'duration_unit.*' => 'nullable|string',
            'status' => 'nullable|array',
            'status.*' => 'nullable|string',
            'drug_name' => 'nullable|array',
            'drug_name.*' => 'nullable|string',
            'drug_dose' => 'nullable|array',
            'drug_dose.*' => 'nullable|string',
            'drug_frequency' => 'nullable|array',
            'drug_frequency.*' => 'nullable|string',
            'drug_for' => 'nullable|array',
            'drug_for.*' => 'nullable|string',
        ]);

        // Save disease history
        if (!empty($validated['name'])) {
            foreach ($validated['name'] as $index => $name) {
                if (!empty($name)) { // Only save if disease name is not empty
                    DiseaseHistory::create([
                        'patient_id' => $validated['patient_id'],
                        'name' => $name,
                        'duration_value' => $validated['duration_value'][$index] ?? null,
                        'duration_unit' => $validated['duration_unit'][$index] ?? null,
                        'status' => $validated['status'][$index] ?? 0,
                    ]);
                }
            }
        }

        // Save drug history
        if (!empty($validated['drug_name'])) {
            foreach ($validated['drug_name'] as $index => $drugName) {
                if (!empty($drugName)) { // Only save if drug name is not empty
                    \App\Models\DrugHistory::create([
                        'patient_id' => $validated['patient_id'],
                        'drug_name' => $drugName,
                        'drug_dose' => $validated['drug_dose'][$index] ?? null,
                        'drug_frequency' => $validated['drug_frequency'][$index] ?? null,
                        'drug_for' => $validated['drug_for'][$index] ?? null,
                        'dose_unit' => $validated['dose_unit'][$index] ?? null,
                        'status' => $validated['drug_status'][$index] ?? 1,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Disease and drug history saved successfully.');
    }

    public function storeLabOrders(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'lab_orders' => 'required|array|min:1',
            'lab_orders.*.service_id' => 'required|integer',
            'lab_orders.*.service_type' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Create one LabOrder per service (test or group)
            foreach ($data['lab_orders'] as $index => $order) {
                Log::info("Processing service #{$index}", $order);

                LabOrder::create([
                    'appointment_id' => $data['appointment_id'],
                    'service_type' => $order['service_type'],
                    'service_id' => $order['service_id'],
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Lab orders saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving lab orders: '.$e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving lab orders. Please try again.');
        }
    }

    /**
     * Store a new diagnosis
     */
    public function storeDiagnosis(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'primary_diagnosis' => 'nullable|string|max:255',
            'secondary_diagnosis' => 'nullable|string|max:255',
            'follow_up_days' => 'nullable|integer',
        ]);

        $data['status'] = $data['status'] ?? 'active';

        Diagnosis::create($data);
        return redirect()->back()->with('success', 'Diagnosis saved successfully.');
    }

    /**
     * Update an existing diagnosis
     */
    public function updateDiagnosis(Request $request, $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        $data = $request->validate([
            'diagnosis_name' => 'required|string|max:255',
            'primary_diagnosis' => 'nullable|string|max:255',
            'secondary_diagnosis' => 'nullable|string|max:255',
            'follow_up_days' => 'nullable|integer',
        ]);

        $diagnosis->update($data);
        return redirect()->back()->with('success', 'Diagnosis updated successfully.');
    }

    /**
     * Delete a diagnosis
     */
    public function destroyDiagnosis($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);
        $diagnosis->delete();
        return redirect()->back()->with('success', 'Diagnosis deleted successfully.');
    }

    /**
     * Store a new medication
     */
    public function storeMedication(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'drug_name' => 'required|string|max:255',
            'route' => 'nullable|in:oral,intramuscular,intravenous,subcutaneous,topical,inhalation,rectal,sublingual',
            'dose' => 'nullable|string',
            'dose_unit' => 'nullable|string',
            'frequency' => 'nullable|in:once_daily,twice_daily,thrice_daily,four_times_daily,as_needed,every_4_hours,every_6_hours,every_8_hours,every_12_hours,weekly',
            'duration_value' => 'nullable|integer',
            'duration_unit' => 'nullable|in:days,weeks,months',
            'instructions' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'active';
        $data['route'] = $data['route'] ?? 'oral';

        Medication::create($data);
        return redirect()->back()->with('success', 'Medication saved successfully.');
    }

    /**
     * Update an existing medication
     */
    public function updateMedication(Request $request, $id)
    {
        $medication = Medication::findOrFail($id);

        $data = $request->validate([
            'drug_name' => 'required|string|max:255',
            'route' => 'nullable|in:oral,intramuscular,intravenous,subcutaneous,topical,inhalation,rectal,sublingual',
            'dose' => 'nullable|string',
            'dose_unit' => 'nullable|string',
            'frequency' => 'nullable|in:once_daily,twice_daily,thrice_daily,four_times_daily,as_needed,every_4_hours,every_6_hours,every_8_hours,every_12_hours,weekly',
            'duration_value' => 'nullable|integer',
            'duration_unit' => 'nullable|in:days,weeks,months',
            'instructions' => 'nullable|string',
            'status' => 'nullable|in:active,completed,discontinued',
        ]);

        $medication->update($data);
        return redirect()->back()->with('success', 'Medication updated successfully.');
    }

    /**
     * Delete a medication
     */
    public function destroyMedication($id)
    {
        $medication = Medication::findOrFail($id);
        $medication->delete();
        return redirect()->back()->with('success', 'Medication deleted successfully.');
    }

}
