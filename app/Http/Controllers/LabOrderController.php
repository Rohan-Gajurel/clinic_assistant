<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use Illuminate\Http\Request;

class LabOrderController extends Controller
{
    public function show($id)
    {
        $labOrder = LabOrder::with([
            'services.service_',
            'appointment.patient',
            'appointment.doctor.user',
        ])->findOrFail($id);

        return view('backend.lab_orders.show', compact('labOrder'));
    }
}
