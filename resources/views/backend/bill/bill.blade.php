@extends('backend.layout.app')

@section('title')
    <title>Bills - TeleMed Admin</title>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="color: var(--secondary-color); font-weight: 600;">Bill Management</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" style="color: var(--primary-color);">Dashboard</a></li>
                <li class="breadcrumb-item active">Bills</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bills.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Generate New Bill
    </a>
</div>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@elseif(session('deleted'))
    <div class="alert alert-danger">{{ session('deleted') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            <i class="bi bi-receipt me-2" style="color: var(--primary-color);"></i>All Bills
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bill ID</th>
                        <th>Patient</th>
                        <th>Date</th>
                        <th class="text-end">Gross Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $bill->id }}</td>
                            <td>{{ $bill->patient->full_name ?? 'N/A' }}</td>
                            <td>{{ $bill->created_at->format('d M, Y') }}</td>
                            <td class="text-end">Rs. {{ number_format($bill->gross_amount, 2) }}</td>
                            <td class="text-end text-danger">Rs. {{ number_format($bill->discount_amount, 2) }}</td>
                            <td class="text-end fw-bold">Rs. {{ number_format($bill->net_amount, 2) }}</td>
                            <td class="text-center">
                                @if($bill->status == 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($bill->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('bills.edit', $bill->id) }}" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bill?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No bills found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-transparent">
        <span class="text-muted">Showing {{ count($bills) }} bills</span>
    </div>
</div>

<!-- Doctor Orders Section -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-clipboard-check me-2" style="color: var(--primary-color);"></i>Lab Orders Made by Doctors
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Doctor</th>
                        <th>Patient</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $labOrders = \App\Models\LabOrder::with(['appointment.doctor.user', 'appointment.patient'])->latest()->take(20)->get();
                @endphp
                @forelse($labOrders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->appointment && $order->appointment->doctor ? ($order->appointment->doctor->user->name ?? 'N/A') : 'N/A' }}</td>
                        <td>{{ $order->appointment && $order->appointment->patient ? ($order->appointment->patient->full_name ?? 'N/A') : 'N/A' }}</td>
                        <td>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d M, Y') : '' }}</td>
                        <td>
                            <a href="{{ route('lab-orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No lab orders found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
