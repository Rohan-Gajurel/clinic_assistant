@extends('backend.layout.app')

@section('title')
    <title>Result Dispatch - MediNest Lab</title>
@endsection

@section('page-title', 'Result Dispatch')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">
            <i class="bi bi-send me-2" style="color: var(--primary-color);"></i>Results Ready for Dispatch
        </h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Search patient..." style="width: 200px;" id="searchInput">
            <input type="date" class="form-control" style="width: 150px;">
        </div>
    </div>
    <div class="card-body">
        @forelse($completedBills ?? [] as $bill)
            @php
                $completedItems = $bill->items->where('sample_status', 'completed');
            @endphp
            @if($completedItems->count() > 0)
            <div class="card mb-3 border bill-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            {{ strtoupper(substr(optional($bill->patient)->full_name ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 patient-name">{{ optional($bill->patient)->full_name ?? 'N/A' }}</h6>
                            <small class="text-muted">
                                Bill #{{ $bill->id }} | {{ optional($bill->patient)->contact_number ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-success">{{ $completedItems->count() }} ready</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Sample ID</th>
                                <th style="width: 25%;">Test Name</th>
                                <th style="width: 20%;">Completed At</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 25%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedItems as $item)
                            <tr>
                                <td>
                                    <strong class="text-success">{{ $item->sample_id ?? 'SMP-' . $item->id }}</strong>
                                </td>
                                <td>
                                    {{ $item->service->name ?? $item->description ?? 'Unknown Test' }}
                                </td>
                                <td>
                                    {{ $item->updated_at ? $item->updated_at->format('d M, Y H:i') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-success">Ready</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#dispatchModal{{ $item->id }}">
                                        <i class="bi bi-send me-1"></i>Dispatch
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Dispatch Modal -->
                            <div class="modal fade" id="dispatchModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">
                                                <i class="bi bi-send me-2"></i>Dispatch Report
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('lab.dispatch-result', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="alert alert-info mb-3">
                                                    <strong>Sample:</strong> {{ $item->sample_id ?? 'SMP-' . $item->id }}<br>
                                                    <strong>Test:</strong> {{ $item->service->name ?? $item->description ?? 'Unknown' }}<br>
                                                    <strong>Patient:</strong> {{ optional($bill->patient)->full_name ?? 'N/A' }}
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Dispatch Method <span class="text-danger">*</span></label>
                                                    <select name="dispatch_method" class="form-select" required>
                                                        <option value="">Select Method...</option>
                                                        <option value="hand_delivery">Hand Delivery</option>
                                                        <option value="email">Email</option>
                                                        <option value="sms">SMS Notification</option>
                                                        <option value="courier">Courier</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Dispatch Time <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" name="dispatch_time" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Received By</label>
                                                    <input type="text" name="received_by" class="form-control" placeholder="Name of person receiving the report">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Dispatch Notes</label>
                                                    <textarea name="dispatch_notes" class="form-control" rows="2" placeholder="Any dispatch notes..."></textarea>
                                                </div>
                                                
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="send_notification" id="sendNotif{{ $item->id }}" checked>
                                                    <label class="form-check-label" for="sendNotif{{ $item->id }}">
                                                        Send notification to patient
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-send me-1"></i>Mark as Dispatched
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
            @endif
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p>No results ready for dispatch</p>
                <small>Results with status "completed" will appear here for dispatch</small>
            </div>
        @endforelse
    </div>
</div>

@push('script')
<script>
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.bill-card').forEach(card => {
            const patientName = card.querySelector('.patient-name')?.textContent.toLowerCase() || '';
            card.style.display = patientName.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection
