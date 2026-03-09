@extends('backend.layout.app')

@section('title')
    <title>Bill Details - TeleMed Admin</title>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-semibold">Bill Details</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bills.index') }}">Bills</a>
                </li>
                <li class="breadcrumb-item active">Bill #{{ $bill->id }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Bill ID</small>
                <strong>#{{ $bill->id }}</strong>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Bill Date</small>
                <strong>{{ optional($bill->created_at)->format('d M, Y') ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Status</small>
                @if($bill->status === 'confirmed')
                    <span class="badge bg-success">Confirmed</span>
                @elseif($bill->status === 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                @else
                    <span class="badge bg-warning">Pending</span>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">Patient</small>
                <strong>{{ optional($bill->patient)->full_name ?? 'N/A' }}</strong>
            </div>
        </div>
        @if($bill->patient)
        <div class="row">
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Age</small>
                <strong>{{ $bill->patient->age ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Gender</small>
                <strong>{{ ucfirst($bill->patient->sex ?? 'N/A') }}</strong>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted d-block">Contact</small>
                <strong>{{ $bill->patient->contact_number ?? 'N/A' }}</strong>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-check me-2"></i>Bill Items
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Service / Item</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 15%;">Rate (Rs.)</th>
                        <th style="width: 15%;">Amount (Rs.)</th>
                        <th style="width: 15%;">Net (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bill->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->service_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->rate, 2) }}</td>
                            <td>{{ number_format($item->amount, 2) }}</td>
                            <td>{{ number_format($item->net_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                No items found for this bill.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        @if($bill->cancellation_reason)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Cancellation Reason</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ $bill->cancellation_reason }}</p>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title mb-3">Bill Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Amount:</span>
                    <span class="fw-semibold">Rs. {{ number_format($bill->gross_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Discount:</span>
                    <span class="text-danger">- Rs. {{ number_format($bill->discount_amount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Amount:</span>
                    <span class="fw-bold text-primary fs-5">Rs. {{ number_format($bill->net_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
