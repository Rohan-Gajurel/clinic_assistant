<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\LabResult;
use App\Models\LabOrder;
use Illuminate\Http\Request;

class LabController extends Controller
{
    /**
     * Sample Collection Page - Shows bills with pending status for sample collection
     */
    public function sampleCollection()
    {
        // Get bills with pending status that have items with pending sample collection
        $pendingBills = Bill::with(['patient', 'items' => function($query) {
                $query->where('sample_status', 'pending');
            }])
            ->whereHas('items', function($query) {
                $query->where('sample_status', 'pending');
            })
            ->where('status', 'pending') // Only pending bills
            ->latest()
            ->get();

        return view('backend.lab.sample-collection', compact('pendingBills'));
    }

    /**
     * Collect Sample for a bill item - updates bill to confirmed when all samples collected
     */
    public function collectSample(Request $request, $id)
    {
        $request->validate([
            'sample_id' => 'required|string',
            'collection_time' => 'required',
            'collected_by' => 'required|string',
        ]);

        $billItem = BillItem::findOrFail($id);
        $billItem->update([
            'sample_status' => 'collected',
            'sample_id' => $request->sample_id,
            'collected_at' => $request->collection_time,
            'collected_by' => $request->collected_by,
            'collection_notes' => $request->notes,
        ]);

        // Check if all items in the bill have been collected
        $bill = $billItem->bill;
        $pendingItems = $bill->items()->where('sample_status', 'pending')->count();
        
        // Update bill status to confirmed only when all items are collected
        if ($pendingItems === 0) {
            $bill->update(['status' => 'confirmed']);
            return redirect()->route('lab.sample-collection')
                ->with('success', 'All samples collected! Bill confirmed.');
        }

        return redirect()->route('lab.sample-collection')
            ->with('success', "Sample collected! {$pendingItems} item(s) remaining.");
    }

    /**
     * Result Entries Page - Shows bill items with collected samples awaiting results
     */
    public function resultEntries()
    {
        // Show any bill that has items with collected samples (regardless of bill status)
        $collectedBills = Bill::with(['patient', 'items' => function($query) {
                $query->where('sample_status', 'collected');
            }, 'items.service', 'items.labResults'])
            ->whereHas('items', function($query) {
                $query->where('sample_status', 'collected');
            })
            ->latest()
            ->get();
        
        // Eager load tests for LabGroup services
        foreach ($collectedBills as $bill) {
            foreach ($bill->items as $item) {
                if ($item->isLabGroup() && $item->service) {
                    $item->service->load('tests.method');
                } elseif ($item->isLabTest() && $item->service) {
                    $item->service->load('method');
                }
            }
        }

        return view('backend.lab.result-entries', compact('collectedBills'));
    }

    /**
     * Enter Results for a bill item test
     */
    public function enterResults(Request $request, $id)
    {
        $request->validate([
            'lab_test_id' => 'required|exists:lab_tests,id',
            'result_value' => 'required',
            'result_status' => 'required|string',
            'technician' => 'required|string',
        ]);

        $billItem = BillItem::findOrFail($id);
        
        // Determine if result is numeric or text
        $resultType = $request->input('result_type', 'text');
        
        // Create lab result record
        LabResult::create([
            'bill_item_id' => $billItem->id,
            'lab_test_id' => $request->lab_test_id,
            'numeric_value' => $resultType === 'numeric' ? $request->result_value : null,
            'text_value' => $resultType !== 'numeric' ? $request->result_value : null,
            'reference_from' => $request->reference_from,
            'reference_to' => $request->reference_to,
            'unit' => $request->unit,
            'status' => $request->result_status,
            'remarks' => $request->remarks,
            'entered_by' => $request->technician,
            'entered_at' => $request->tested_at ?? now(),
        ]);

        // Check if all tests for this bill item have results
        $tests = $billItem->getTests();
        $enteredResults = $billItem->labResults()->count();
        
        // Update sample_status to completed only when all tests have results
        if ($tests->count() > 0 && $enteredResults >= $tests->count()) {
            $billItem->update(['sample_status' => 'completed']);
            return redirect()->route('lab.result-entries')
                ->with('success', 'All results entered! Item marked as completed.');
        }

        return redirect()->route('lab.result-entries')
            ->with('success', 'Result saved successfully! ' . ($tests->count() - $enteredResults) . ' test(s) remaining.');
    }

    /**
     * Result Dispatch Page - Shows bill items with completed results ready for dispatch
     */
    public function resultDispatch()
    {
        $completedBills = Bill::with(['patient', 'items' => function($query) {
                $query->where('sample_status', 'completed');
            }])
            ->whereHas('items', function($query) {
                $query->where('sample_status', 'completed');
            })
            ->where('status', 'confirmed')
            ->latest()
            ->get();

        return view('backend.lab.result-dispatch', compact('completedBills'));
    }

    /**
     * Dispatch Result for a bill item
     */
    public function dispatchResult(Request $request, $id)
    {
        $request->validate([
            'dispatch_method' => 'required|string',
        ]);

        $billItem = BillItem::findOrFail($id);
        $billItem->update([
            'sample_status' => 'dispatched',
        ]);

        // Send notification if requested
        if ($request->has('send_notification')) {
            // TODO: Implement notification logic
        }

        return redirect()->route('lab.result-dispatch')
            ->with('success', 'Report dispatched successfully!');
    }

    /**
     * Print Report
     */
    public function printReport($id)
    {
        $labOrder = LabOrder::with(['appointment.patient', 'appointment.doctor.user', 'services.labGroup'])
            ->findOrFail($id);

        return view('backend.lab.print-report', compact('labOrder'));
    }
}
