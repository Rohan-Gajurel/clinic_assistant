<?php


use App\Models\LabGroup;
use App\Models\LabTest;

function searchServices($query)
{
    if (!$query) {
        return response()->json([]);
    }

    // Search Lab Tests
    $labTests = LabTest::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('code', 'like', "%{$query}%");
        })
        ->select('id', 'name', 'code', 'price')
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'price' => $item->price,
                'type' => 'App\\Models\\LabTest'
            ];
        });
    $labgroups = LabGroup::where('name', 'like', "%{$query}%")
        ->select('id', 'name','charge_amount')
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'code' => null,
                'price' => $item->charge_amount,
                'type' => 'App\\Models\\LabGroup'
            ];
        });

    return response()->json($labTests->concat($labgroups));
}


?>