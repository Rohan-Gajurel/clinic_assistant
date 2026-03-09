@extends('backend.layout.app')

@section('title')
    <title>नमुना संग्रह (Sample Collection) - MediNest Lab</title>
@endsection

@section('page-title', 'नमुना संग्रह (Sample Collection)')

@section('content')
<div @class(['card'])>
    <div @class(['card-header', 'd-flex', 'justify-content-between', 'align-items-center'])>
        <h5 @class(['card-title'])>
            <i @class(['bi', 'bi-collection', 'me-2']) style="color: var(--primary-color);"></i>Pending Sample Collection
        </h5>
        <div @class(['d-flex', 'gap-2'])>
            <input type="text" @class(['form-control']) placeholder="Search patient..." style="width: 200px;">
            <input type="date" @class(['form-control']) style="width: 150px;">
        </div>
    </div>
    <div @class(['card-body', 'p-0'])>
        @forelse($pendingBills ?? [] as $index => $bill)
            <div @class(['border-bottom', 'p-3'])>
                <!-- Bill Header -->
                <div @class(['d-flex', 'justify-content-between', 'align-items-center', 'mb-3'])>
                    <div @class(['d-flex', 'align-items-center'])>
                        <div @class(['rounded-circle', 'bg-primary', 'text-white', 'd-flex', 'align-items-center', 'justify-content-center', 'me-3']) style="width: 45px; height: 45px; font-size: 1rem;">
                            {{ strtoupper(substr(optional($bill->patient)->full_name ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <h6 @class(['mb-0'])>{{ optional($bill->patient)->full_name ?? 'N/A' }}</h6>
                            <small @class(['text-muted'])>
                                Bill #{{ $bill->id }} | {{ optional($bill->created_at)->format('d M, Y') }}
                                | Contact: {{ optional($bill->patient)->contact_number ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                    <span @class(['badge', 'bg-warning'])>{{ $bill->items->count() }} items pending</span>
                </div>

                <!-- Bill Items Table -->
                <div @class(['table-responsive'])>
                    <table @class(['table', 'table-sm', 'table-bordered', 'mb-0'])>
                        <thead @class(['table-light'])>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 35%;">Service/Test Name</th>
                                <th style="width: 10%;">Qty</th>
                                <th style="width: 15%;">Rate</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->items as $itemIndex => $item)
                                <tr>
                                    <td>{{ $itemIndex + 1 }}</td>
                                    <td><strong>{{ $item->service_name }}</strong></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rs. {{ number_format($item->rate, 2) }}</td>
                                    <td>
                                        <span @class(['badge', 'bg-warning'])>Pending</span>
                                    </td>
                                    <td>
                                        <button @class(['btn', 'btn-sm', 'btn-primary']) data-bs-toggle="modal" data-bs-target="#collectModal{{ $item->id }}">
                                            <i @class(['bi', 'bi-droplet', 'me-1'])></i>Collect
                                        </button>
                                    </td>
                                </tr>

                                <!-- Collect Sample Modal for each item -->
                                <div @class(['modal', 'fade']) id="collectModal{{ $item->id }}" tabindex="-1">
                                    <div @class(['modal-dialog'])>
                                        <div @class(['modal-content'])>
                                            <div @class(['modal-header'])>
                                                <h5 @class(['modal-title'])>Collect Sample - {{ $item->service_name }}</h5>
                                                <button type="button" @class(['btn-close']) data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('lab.collect-sample', $item->id) }}" method="POST">
                                                @csrf
                                                <div @class(['modal-body'])>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Patient</label>
                                                        <input type="text" @class(['form-control']) value="{{ optional($bill->patient)->full_name ?? 'N/A' }}" readonly>
                                                    </div>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Test/Service</label>
                                                        <input type="text" @class(['form-control']) value="{{ $item->service_name }}" readonly>
                                                    </div>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Sample ID</label>
                                                        <input type="text" name="sample_id" @class(['form-control']) value="SMP-{{ date('Ymd') }}-{{ $item->id }}" required>
                                                    </div>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Collection Time</label>
                                                        <input type="datetime-local" name="collection_time" @class(['form-control']) value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Collected By</label>
                                                        <input type="text" name="collected_by" @class(['form-control']) value="{{ auth()->user()->name ?? '' }}" required>
                                                    </div>
                                                    <div @class(['mb-3'])>
                                                        <label @class(['form-label'])>Notes</label>
                                                        <textarea name="notes" @class(['form-control']) rows="2" placeholder="Any special notes..."></textarea>
                                                    </div>
                                                </div>
                                                <div @class(['modal-footer'])>
                                                    <button type="button" @class(['btn', 'btn-secondary']) data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" @class(['btn', 'btn-primary'])>
                                                        <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Mark as Collected
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div @class(['text-center', 'py-5', 'text-muted'])>
                <i @class(['bi', 'bi-inbox', 'fs-1', 'd-block', 'mb-2'])></i>
                No pending samples for collection
            </div>
        @endforelse
    </div>
</div>
@endsection
