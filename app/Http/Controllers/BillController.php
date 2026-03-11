<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\LabOrder;
use App\Models\LabTest;
use App\Models\Patient;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('patient')->latest()->get();
        return view('backend.bill.bill', compact('bills'));
    }

    public function create(Request $request)
    {
        $labOrders = collect();
        $patient = null;
        $appointment = null;

        $appointmentId = $request->query('appointment_id');
        if ($appointmentId) {
            $labOrders = LabOrder::with(['service', 'appointment.patient'])
                ->where('appointment_id', $appointmentId)
                ->get();
            
            if ($labOrders->isNotEmpty()) {
                $appointment = $labOrders->first()->appointment;
                $patient = $appointment->patient;
            }
        }

        return view('backend.bill.create', compact('labOrders', 'patient', 'appointment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'gross_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.service_type' => 'nullable|string|max:255',
            'items.*.service_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.net_amount' => 'required|numeric|min:0',
        ]);

        $bill = Bill::create([
            'patient_id' => $data['patient_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'gross_amount' => $data['gross_amount'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'net_amount' => $data['net_amount'],
            'status' => 'pending',
        ]);

        foreach ($data['items'] as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'service_type' => $item['service_type'] ?? null,
                'service_id' => $item['service_id'] ?? null,
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
                'discount' => $item['discount'] ?? 0,
                'net_amount' => $item['net_amount'],
                'sample_status' => 'pending',
            ]);
        }

        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    public function show($id)
    {
        $bill = Bill::with(['patient', 'items'])->findOrFail($id);
        return view('backend.bill.show', compact('bill'));
    }

    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);
        $bill->delete();
        return redirect()->route('bills.index')->with('deleted', 'Bill deleted successfully.');
    }

    public function patientSearch(Request $request)
    {
        try {
            $query = $request->input('query');
            $patients = Patient::where('full_name', 'like', "%$query%")
                ->orWhere('contact_number', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->limit(10)
                ->get();

            return response()->json($patients);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function serviceSearch(Request $request)
    {
        return searchServices($request->input('query'));
    }

    public function edit($id)
    {
        $bill = Bill::with('items')->findOrFail($id);
        return view('backend.bill.edit', compact('bill'));
    }

    public function update(Request $request, $id)
    {
        $bill = Bill::findOrFail($id);

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'gross_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.service_type' => 'nullable|string|max:255',
            'items.*.service_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.net_amount' => 'required|numeric|min:0',
        ]);

        $bill->update([
            'patient_id' => $data['patient_id'],
            'gross_amount' => $data['gross_amount'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'net_amount' => $data['net_amount'],
        ]);

        // Delete existing items and recreate them
        $bill->items()->delete();
        foreach ($data['items'] as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'service_type' => $item['service_type'] ?? null,
                'service_id' => $item['service_id'] ?? null,
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
                'discount' => $item['discount'] ?? 0,
                'net_amount' => $item['net_amount'],
                'sample_status' => 'pending',
            ]);
        }

        return redirect()->route('bills.index')->with('success', 'Bill updated successfully.');
    }
}
