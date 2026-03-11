@extends('backend.layout.app')

@section('title')
    <title>Lab Order Details - MediNest Admin</title>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-semibold">Lab Order Details</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bills.index') }}">Bills</a></li>
                <li class="breadcrumb-item active">Lab Order #{{ $labOrder->id }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Order ID</small>
                <strong>#{{ $labOrder->id }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Order Date</small>
                <strong>{{ optional($labOrder->created_at)->format('d M, Y') ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Appointment ID</small>
                <strong>{{ $labOrder->appointment_id }}</strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Patient</small>
                <strong>{{ optional($labOrder->appointment->patient)->full_name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Doctor</small>
                <strong>{{ optional(optional($labOrder->appointment->doctor)->user)->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Contact</small>
                <strong>{{ optional($labOrder->appointment->patient)->contact_number ?? 'N/A' }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-flask me-2"></i>Ordered Test / Service
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Service</th>
                        <th style="width: 20%;">Type</th>
                        <th style="width: 15%;">Price (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $model = $labOrder->service;
                        $name = $model->name ?? 'N/A';
                        $type = $labOrder->service_type == 'App\\Models\\LabTest' ? 'Lab Test' : ($labOrder->service_type == 'App\\Models\\LabGroup' ? 'Lab Group' : 'Service');
                        $price = $model->price ?? $model->charge_amount ?? 0;
                    @endphp
                    @if($model)
                        <tr>
                            <td>1</td>
                            <td>{{ $name }}</td>
                            <td><span class="badge bg-{{ $labOrder->service_type == 'App\\Models\\LabTest' ? 'info' : 'primary' }}">{{ $type }}</span></td>
                            <td>Rs. {{ number_format($price, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                No service found for this order.
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if($model)
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Estimated Total</th>
                            <th>Rs. {{ number_format($price, 2) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@if($labOrder->service && $labOrder->appointment && $labOrder->appointment->patient)
    <div class="d-flex justify-content-end">
        <a href="{{ route('bills.create', ['appointment_id' => $labOrder->appointment_id]) }}"
           class="btn btn-primary">
            <i class="bi bi-receipt me-2"></i>Generate Bill for Appointment
        </a>
    </div>
@endif

@endsection

