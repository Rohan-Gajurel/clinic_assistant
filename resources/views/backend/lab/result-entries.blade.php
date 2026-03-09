@extends('backend.layout.app')

@section('title')
    <title>Result Entries - MediNest Lab</title>
@endsection

@section('page-title', 'Result Entries')

@section('content')
<div @class(['card'])>
    <div @class(['card-header', 'd-flex', 'justify-content-between', 'align-items-center'])>
        <h5 @class(['card-title'])>
            <i @class(['bi', 'bi-clipboard-data', 'me-2']) style="color: var(--primary-color);"></i>Pending Result Entry
        </h5>
        <div @class(['d-flex', 'gap-2'])>
            <input type="text" @class(['form-control']) placeholder="Search sample ID..." style="width: 200px;">
            <input type="date" @class(['form-control']) style="width: 150px;">
        </div>
    </div>
    <div @class(['card-body', 'p-0'])>
        @forelse($collectedBills ?? [] as $index => $bill)
            <div @class(['border-bottom', 'p-3'])>
                <!-- Bill Header -->
                <div @class(['d-flex', 'justify-content-between', 'align-items-center', 'mb-3'])>
                    <div @class(['d-flex', 'align-items-center'])>
                        <div @class(['rounded-circle', 'bg-info', 'text-white', 'd-flex', 'align-items-center', 'justify-content-center', 'me-3']) style="width: 45px; height: 45px; font-size: 1rem;">
                            {{ strtoupper(substr(optional($bill->patient)->full_name ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <h6 @class(['mb-0'])>{{ optional($bill->patient)->full_name ?? 'N/A' }}</h6>
                            <small @class(['text-muted'])>
                                Bill #{{ $bill->id }} | {{ optional($bill->created_at)->format('d M, Y') }}
                            </small>
                        </div>
                    </div>
                    <span @class(['badge', 'bg-info'])>{{ $bill->items->count() }} items awaiting results</span>
                </div>

                <!-- Bill Items Table -->
                <div @class(['table-responsive'])>
                    <table @class(['table', 'table-sm', 'table-bordered', 'mb-0'])>
                        <thead @class(['table-light'])>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">Test Name</th>
                                <th style="width: 12%;">Sample ID</th>
                                <th style="width: 10%;">Type</th>
                                <th style="width: 15%;">Reference</th>
                                <th style="width: 10%;">Unit</th>
                                <th style="width: 28%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bill->items as $itemIndex => $item)
                                @php
                                    $labTest = $item->service;
                                    $resultType = optional($labTest)->result_type ?? 'text';
                                    $refFrom = optional($labTest)->reference_from ?? null;
                                    $refTo = optional($labTest)->reference_to ?? null;
                                    $unit = optional($labTest)->unit ?? '';
                                    $referenceRange = ($refFrom && $refTo) ? "{$refFrom} - {$refTo}" : 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $itemIndex + 1 }}</td>
                                    <td><strong>{{ $item->service_name }}</strong></td>
                                    <td><code>{{ $item->sample_id ?? 'N/A' }}</code></td>
                                    <td>
                                        <span class="badge {{ $resultType == 'numeric' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ ucfirst($resultType) }}
                                        </span>
                                    </td>
                                    <td>{{ $referenceRange }}</td>
                                    <td>{{ $unit ?: '-' }}</td>
                                    <td>
                                        <button @class(['btn', 'btn-sm', 'btn-primary']) data-bs-toggle="modal" data-bs-target="#resultModal{{ $item->id }}">
                                            <i @class(['bi', 'bi-pencil', 'me-1'])></i>Enter Results
                                        </button>
                                    </td>
                                </tr>

                                <!-- Result Entry Modal for each item -->
                                <div @class(['modal', 'fade']) id="resultModal{{ $item->id }}" tabindex="-1">
                                    <div @class(['modal-dialog', 'modal-lg'])>
                                        <div @class(['modal-content'])>
                                            <div @class(['modal-header', 'bg-primary', 'text-white'])>
                                                <h5 @class(['modal-title'])>
                                                    <i @class(['bi', 'bi-clipboard-data', 'me-2'])></i>Enter Results - {{ $item->service_name }}
                                                </h5>
                                                <button type="button" @class(['btn-close', 'btn-close-white']) data-bs-dismiss="modal"></button>
                                            </div>
                                    <form action="{{ route('lab.enter-results', $item->id) }}" method="POST">
                                        @csrf
                                        <div @class(['modal-body'])>
                                            <!-- Patient & Sample Info -->
                                            <div @class(['alert', 'alert-light', 'border', 'mb-3'])>
                                                <div @class(['row'])>
                                                    <div @class(['col-md-4'])>
                                                        <small @class(['text-muted'])>Patient</small>
                                                        <div><strong>{{ optional($bill->patient)->full_name ?? 'N/A' }}</strong></div>
                                                    </div>
                                                    <div @class(['col-md-4'])>
                                                        <small @class(['text-muted'])>Sample ID</small>
                                                        <div><strong>{{ $item->sample_id ?? 'N/A' }}</strong></div>
                                                    </div>
                                                    <div @class(['col-md-4'])>
                                                        <small @class(['text-muted'])>Collected At</small>
                                                        <div><strong>{{ optional($item->collected_at)->format('d M, Y H:i') ?? 'N/A' }}</strong></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Test Parameters Info -->
                                            <div @class(['card', 'mb-3'])>
                                                <div @class(['card-header', 'bg-light', 'py-2'])>
                                                    <strong>Test Parameters</strong>
                                                </div>
                                        <div @class(['card-body', 'py-2'])>
                                            <div @class(['row'])>
                                                <div @class(['col-md-3'])>
                                                    <small @class(['text-muted'])>Result Type</small>
                                                    <div>
                                                        <span class="badge {{ $resultType == 'numeric' ? 'bg-primary' : 'bg-secondary' }}">
                                                            {{ ucfirst($resultType) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div @class(['col-md-3'])>
                                                    <small @class(['text-muted'])>Reference Range</small>
                                                    <div><strong>{{ $referenceRange }}</strong></div>
                                                </div>
                                                <div @class(['col-md-3'])>
                                                    <small @class(['text-muted'])>Unit</small>
                                                    <div><strong>{{ $unit ?: 'N/A' }}</strong></div>
                                                </div>
                                                <div @class(['col-md-3'])>
                                                    <small @class(['text-muted'])>Method</small>
                                                    <div><strong>{{ optional(optional($labTest)->method)->name ?? 'N/A' }}</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                            
                                            <!-- Result Entry Fields -->
                                            <div @class(['row', 'mb-3'])>
                                                <div @class(['col-md-6'])>
                                                    <label @class(['form-label'])>Result Value <span @class(['text-danger'])>*</span></label>
                                                    @if($resultType == 'numeric')
                                                        <div @class(['input-group'])>
                                                            <input type="number" 
                                                                    name="result_value" 
                                                                    id="resultValue{{ $item->id }}"
                                                                    @class(['form-control']) 
                                                                    step="0.01"
                                                                    placeholder="Enter numeric value" 
                                                                    data-ref-from="{{ $refFrom }}"
                                                                    data-ref-to="{{ $refTo }}"
                                                                    data-status-field="resultStatus{{ $item->id }}"
                                                                    onchange="calculateStatus(this)"
                                                                    required>
                                                            <span @class(['input-group-text'])>{{ $unit }}</span>
                                                        </div>
                                                        <small @class(['text-muted'])>Reference: {{ $referenceRange }} {{ $unit }}</small>
                                                    @else
                                                        <textarea name="result_value" @class(['form-control']) rows="2" placeholder="Enter text result" required></textarea>
                                                    @endif
                                                </div>
                                                <div @class(['col-md-6'])>
                                                    <label @class(['form-label'])>Status <span @class(['text-danger'])>*</span></label>
                                                    <select name="result_status" id="resultStatus{{ $item->id }}" @class(['form-select']) required>
                                                        <option value="">Select Status...</option>
                                                        @if($resultType == 'numeric')
                                                            <option value="normal">Normal</option>
                                                            <option value="low">Low</option>
                                                            <option value="high">High</option>
                                                            <option value="critical_low">Critical Low</option>
                                                            <option value="critical_high">Critical High</option>
                                                        @else
                                                            <option value="positive">Positive</option>
                                                            <option value="negative">Negative</option>
                                                            <option value="normal">Normal</option>
                                                            <option value="abnormal">Abnormal</option>
                                                        @endif
                                                    </select>
                                                    @if($resultType == 'numeric')
                                                        <small @class(['text-muted'])>Auto-calculated based on reference range</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Hidden fields for reference data -->
                                            <input type="hidden" name="reference_from" value="{{ $refFrom }}">
                                            <input type="hidden" name="reference_to" value="{{ $refTo }}">
                                            <input type="hidden" name="unit" value="{{ $unit }}">
                                            <input type="hidden" name="result_type" value="{{ $resultType }}">

                                            <div @class(['row', 'mb-3'])>
                                                <div @class(['col-md-6'])>
                                                    <label @class(['form-label'])>Technician Name <span @class(['text-danger'])>*</span></label>
                                                    <input type="text" name="technician" @class(['form-control']) value="{{ auth()->user()->name ?? '' }}" required>
                                                </div>
                                                <div @class(['col-md-6'])>
                                                    <label @class(['form-label'])>Test Date/Time</label>
                                                    <input type="datetime-local" name="tested_at" @class(['form-control']) value="{{ now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                            </div>

                                            <div @class(['mb-3'])>
                                                <label @class(['form-label'])>Remarks</label>
                                                <textarea name="remarks" @class(['form-control']) rows="2" placeholder="Any additional remarks or observations..."></textarea>
                                            </div>
                                        </div>
                                        <div @class(['modal-footer'])>
                                            <button type="button" @class(['btn', 'btn-secondary']) data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" @class(['btn', 'btn-primary'])>
                                                <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Save Results
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
                No samples awaiting result entry
            </div>
        @endforelse
    </div>
</div>

@push('script')
<script>
    function calculateStatus(input) {
        const value = parseFloat(input.value);
        const refFrom = parseFloat(input.dataset.refFrom);
        const refTo = parseFloat(input.dataset.refTo);
        const statusFieldId = input.dataset.statusField;
        const statusField = document.getElementById(statusFieldId);
        
        if (isNaN(value) || isNaN(refFrom) || isNaN(refTo)) {
            return;
        }
        
        // Calculate critical ranges (20% beyond normal range)
        const range = refTo - refFrom;
        const criticalLow = refFrom - (range * 0.2);
        const criticalHigh = refTo + (range * 0.2);
        
        let status = 'normal';
        
        if (value < criticalLow) {
            status = 'critical_low';
        } else if (value < refFrom) {
            status = 'low';
        } else if (value > criticalHigh) {
            status = 'critical_high';
        } else if (value > refTo) {
            status = 'high';
        } else {
            status = 'normal';
        }
        
        statusField.value = status;
        
        // Visual feedback
        statusField.classList.remove('bg-success', 'bg-warning', 'bg-danger');
        if (status === 'normal') {
            statusField.classList.add('bg-success', 'text-white');
        } else if (status === 'low' || status === 'high') {
            statusField.classList.add('bg-warning');
        } else {
            statusField.classList.add('bg-danger', 'text-white');
        }
    }
</script>
@endpush
@endsection
