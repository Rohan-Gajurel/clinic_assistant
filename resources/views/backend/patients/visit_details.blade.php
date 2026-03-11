@extends('backend.layout.app')

@section('title')
    <title>Patient Visit Details - MediNest Admin</title>
@endsection

@section('content')
<div @class(['d-flex', 'justify-content-between', 'align-items-center', 'mb-4'])>
    <div>
        <h4 @class(['mb-1', 'fw-semibold'])>Patient Visit Details</h4>
        <nav aria-label="breadcrumb">
            <ol @class(['breadcrumb', 'mb-0'])>
                <li @class(['breadcrumb-item'])><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li @class(['breadcrumb-item'])><a href="{{ route('patients.index') }}">Patients</a></li>
                <li @class(['breadcrumb-item', 'active'])>Visit Details</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('patients.index') }}" @class(['btn', 'btn-outline-secondary'])>
        <i @class(['bi', 'bi-arrow-left', 'me-2'])></i>Back
    </a>
</div>

<!-- Patient Header Card -->
<div @class(['card', 'mb-4', 'shadow-sm'])>
    <div @class(['card-body'])>
        <div @class(['row'])>
            <!-- Patient Photo and ID -->
            <div @class(['col-md-2', 'text-center'])>
                <img src="{{ $patient->photo ? asset('storage/' . $patient->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($patient->full_name) . '&background=1bb6b1&color=fff' }}"
                     alt="avatar" @class(['rounded-circle', 'mb-2']) width="80" height="80">
                <div @class(['fw-bold', 'fs-5', 'text-muted'])>ID: {{ $patient->id }}</div>
            </div>
            
            <!-- Patient Info -->
            <div @class(['col-md-10'])>
                <div @class(['row', 'mb-3'])>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Registration Date</small>
                        <strong>{{ optional($patient->created_at)->format('Y-m-d') ?? 'N/A' }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Full Name</small>
                        <strong>{{ $patient->full_name ?? 'N/A' }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Age / Gender</small>
                        <strong>{{ $patient->age ?? 'N/A' }} / {{ ucfirst($patient->sex ?? 'N/A') }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Contact</small>
                        <strong>{{ $patient->contact_number ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div @class(['row', 'mb-3'])>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Address</small>
                        <strong>{{ $patient->address ?? 'N/A' }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Blood Group</small>
                        <strong>{{ $patient->blood_group ?? 'N/A' }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Email</small>
                        <strong>{{ $patient->email ?? 'N/A' }}</strong>
                    </div>
                    <div @class(['col-md-3'])>
                        <small @class(['text-muted', 'd-block'])>Marital Status</small>
                        <strong>{{ ucfirst($patient->marital_status ?? 'N/A') }}</strong>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div @class(['mt-3'])>
                    <button @class(['btn', 'btn-info', 'btn-sm', 'me-2']) title="View visit history" id='visitHistoryBtn'>
                        <i @class(['bi', 'bi-clock-history', 'me-1'])></i>Visit History
                    </button>
                    <button @class(['btn', 'btn-success', 'btn-sm', 'me-2']) title="View patient card">
                        <i @class(['bi', 'bi-card-text', 'me-1'])></i>Patient Card
                    </button>
                    <button @class(['btn', 'btn-primary', 'btn-sm', 'me-2']) title="Refer to another doctor">
                        <i @class(['bi', 'bi-arrow-repeat', 'me-1'])></i>Refer
                    </button>
                    @if(optional($currentAppointment)->status != 'completed')
                    <button @class(['btn', 'btn-danger', 'btn-sm']) title="End current visit">
                        <i @class(['bi', 'bi-x-circle', 'me-1'])></i>End Visit
                    </button>
                    @else
                    <a href="{{ route('appointments.create', $patient->id) }}" @class(['btn', 'btn-warning', 'btn-sm', 'me-2']) title="Book new appointment">
                        <i @class(['bi', 'bi-calendar-plus', 'me-1'])></i>New Appointment
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<ul @class(['nav', 'nav-tabs', 'mb-3']) id="visitTabs" role="tablist">
   
    <li @class(['nav-item']) role="presentation">
        <button @class(['nav-link', 'active']) id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
            <i @class(['bi', 'bi-card-list', 'me-1'])></i>Overview
        </button>
    </li>
     @if(optional($currentAppointment)->status != 'completed')
    <li @class(['nav-item']) role="presentation">
        <button @class(['nav-link']) id="observations-tab" data-bs-toggle="tab" data-bs-target="#observations" type="button" role="tab">
            <i @class(['bi', 'bi-binoculars', 'me-1'])></i>Observations
        </button>
    </li>
    <li @class(['nav-item']) role="presentation">
        <button @class(['nav-link']) id="laborders-tab" data-bs-toggle="tab" data-bs-target="#laborders" type="button" role="tab">
            <i @class(['bi', 'bi-flask', 'me-1'])></i>Lab Orders
        </button>
    </li>
    <li @class(['nav-item']) role="presentation">
        <button @class(['nav-link']) id="diagnosis-tab" data-bs-toggle="tab" data-bs-target="#diagnosis" type="button" role="tab">
            <i @class(['bi', 'bi-file-earmark-text', 'me-1'])></i>Diagnosis
        </button>
    </li>
    <li @class(['nav-item']) role="presentation">
        <button @class(['nav-link']) id="medication-tab" data-bs-toggle="tab" data-bs-target="#medication" type="button" role="tab">
            <i @class(['bi', 'bi-capsule', 'me-1'])></i>Medication
        </button>
    </li>
    @endif
</ul>

<!-- Tab Contents -->
<div @class(['tab-content']) id="visitTabsContent">
    <!-- Overview Tab -->
    <div @class(['tab-pane', 'fade', 'show', 'active']) id="overview" role="tabpanel" aria-labelledby="overview-tab">
        <div class="row">
            <!-- Left Column - Vitals & Examinations -->
            <div class="col-md-4">
                <!-- Vitals Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Vitals</h5>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Blood pressure</span>
                            <span class="text-muted">{{ $vitals->blood_pressure ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Pulse</span>
                            <span class="text-muted">{{ $vitals->heart_rate ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Height</span>
                            <span class="text-muted">{{ $vitals->height ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Weight</span>
                            <span class="text-muted">{{ $vitals->weight ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Temperature</span>
                            <span class="text-muted">{{ $vitals->temperature ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Respiratory rate</span>
                            <span class="text-muted">{{ $vitals->respiratory_rate ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Examinations Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Examinations</h5>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Primary symptoms</span>
                            <span class="text-muted">{{ $examination->primary_symptom ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">For</span>
                            <span class="text-muted">{{ $examination ? ($examination->symptom_duration_value . ' ' . $examination->symptom_duration_unit) : '-' }}</span>
                        </div>
                        @if($examination && $examination->other_symptoms)
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Other symptoms</span>
                            <span class="text-muted">{{ $examination->other_symptoms }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Visit Details with Tabs -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2">Visit Details</h5>
                        
                        @if($currentAppointment)
                        <div class="alert {{ $currentAppointment->status == 'completed' ? 'alert-secondary' : 'alert-info' }} py-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <strong>{{ \Carbon\Carbon::parse($currentAppointment->appointment_date)->format('d M, Y') }}</strong>
                                    at <strong>{{ \Carbon\Carbon::parse($currentAppointment->start_time)->format('h:i A') }}</strong>
                                    @if($currentAppointment->doctor)
                                        | Dr. {{ $currentAppointment->doctor->user->name ?? 'N/A' }}
                                    @endif
                                </div>
                                <span class="badge {{ $currentAppointment->status == 'completed' ? 'bg-secondary' : 'bg-success' }}">
                                    {{ ucfirst($currentAppointment->status) }}
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Inner Tabs -->
                        <ul class="nav nav-tabs" id="visitDetailsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active text-primary fw-semibold" id="medical-details-tab" data-bs-toggle="tab" data-bs-target="#medical-details" type="button" role="tab">
                                    MEDICAL DETAILS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-primary fw-semibold" id="lab-results-tab" data-bs-toggle="tab" data-bs-target="#lab-results" type="button" role="tab">
                                    LAB RESULTS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-primary fw-semibold" id="medication-details-tab" data-bs-toggle="tab" data-bs-target="#medication-details" type="button" role="tab">
                                    MEDICATION
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3" id="visitDetailsTabContent">
                            <!-- Medical Details Tab -->
                            <div class="tab-pane fade show active" id="medical-details" role="tabpanel">
                                <h5 class="fw-bold mb-3">Diagnosis</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 30%;">Primary Diagnosis</th>
                                            <th style="width: 30%;">Secondary Diagnosis</th>
                                            <th style="width: 17%;">Follow-up (Days)</th>
                                            <th style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($diagnoses as $index => $diagnosis)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $diagnosis->primary_diagnosis }}</td>
                                            <td>{{ $diagnosis->secondary_diagnosis ?? '-' }}</td>
                                            <td>{{ $diagnosis->follow_up_days ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('visit.destroyDiagnosis', $diagnosis->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this diagnosis?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No diagnosis records</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Disease History Section -->
                                <h5 class="fw-bold mb-3 mt-4">Disease History</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 30%;">Disease Name</th>
                                            <th style="width: 20%;">Duration</th>
                                            <th style="width: 20%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($diseaseHistories as $index => $disease)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $disease->name }}</td>
                                            <td>{{ $disease->duration_value ?? '-' }} {{ ucfirst($disease->duration_unit ?? '') }}</td>
                                            <td>
                                                <span class="badge {{ $disease->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $disease->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No disease history</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Lab Results Tab -->
                            <div class="tab-pane fade" id="lab-results" role="tabpanel">
                                <!-- Lab Orders Section -->
                                <h5 class="fw-bold mb-3">Lab Orders</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 35%;">Test/Group Name</th>
                                            <th style="width: 15%;">Type</th>
                                            <th style="width: 20%;">Status</th>
                                            <th style="width: 12%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($labOrders as $index => $labOrder)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $labOrder->service->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($labOrder->service_type == 'App\\Models\\LabTest')
                                                    <span class="badge bg-info">Test</span>
                                                @elseif($labOrder->service_type == 'App\\Models\\LabGroup')
                                                    <span class="badge bg-primary">Group</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $labOrder->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($labOrder->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('lab-orders.show', $labOrder->id) }}" class="btn btn-sm btn-outline-info" title="View Order">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No lab orders</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Dispatched Lab Results Section -->
                                @if(isset($dispatchedResults) && $dispatchedResults->count() > 0)
                                <h5 class="fw-bold mb-3 mt-4">
                                    <i class="bi bi-clipboard-check text-success me-2"></i>Dispatched Lab Results
                                </h5>
                                @foreach($dispatchedResults as $item)
                                    @php
                                        $isLabGroup = $item->isLabGroup();
                                        $tests = $item->getTests();
                                    @endphp
                                    <div class="card mb-3 border-success">
                                        <div class="card-header bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $item->service_name }}</strong>
                                                @if($isLabGroup)
                                                    <span class="badge bg-primary ms-2">Group ({{ $tests->count() }} tests)</span>
                                                @else
                                                    <span class="badge bg-info ms-2">Single Test</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Dispatched
                                                </span>
                                                <small class="text-muted ms-2">Sample: {{ $item->sample_id ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 5%;">#</th>
                                                        <th style="width: 25%;">Test Name</th>
                                                        <th style="width: 20%;">Result</th>
                                                        <th style="width: 20%;">Reference Range</th>
                                                        <th style="width: 15%;">Status</th>
                                                        <th style="width: 15%;">Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($item->labResults as $resultIndex => $result)
                                                        @php
                                                            $statusClass = match($result->status) {
                                                                'normal' => 'bg-success',
                                                                'low', 'high' => 'bg-warning',
                                                                'critical_low', 'critical_high', 'critical' => 'bg-danger',
                                                                default => 'bg-secondary'
                                                            };
                                                            $resultValue = $result->numeric_value ?? $result->text_value ?? 'N/A';
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $resultIndex + 1 }}</td>
                                                            <td><strong>{{ $result->labTest->name ?? 'Unknown Test' }}</strong></td>
                                                            <td>
                                                                <span class="fw-bold {{ in_array($result->status, ['low', 'high', 'critical_low', 'critical_high', 'critical']) ? 'text-danger' : '' }}">
                                                                    {{ $resultValue }} {{ $result->unit ?? '' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($result->reference_from && $result->reference_to)
                                                                    {{ $result->reference_from }} - {{ $result->reference_to }} {{ $result->unit ?? '' }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $statusClass }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $result->remarks ?? '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No results recorded</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer bg-light small text-muted">
                                            <i class="bi bi-person me-1"></i>Technician: {{ $item->labResults->first()->entered_by ?? 'N/A' }}
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-calendar me-1"></i>Entered: {{ optional($item->labResults->first()->entered_at)->format('d M, Y H:i') ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                            </div>

                            <!-- Medication Tab -->
                            <div class="tab-pane fade" id="medication-details" role="tabpanel">
                                <h5 class="fw-bold mb-3">Current Visit Medications</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 5%;">S.N</th>
                                            <th style="width: 18%;">Drug Name</th>
                                            <th style="width: 10%;">Route</th>
                                            <th style="width: 12%;">Dose</th>
                                            <th style="width: 15%;">Frequency</th>
                                            <th style="width: 15%;">Duration</th>
                                            <th style="width: 17%;">Instructions</th>
                                            <th style="width: 8%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($medications as $index => $med)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $med->drug_name }}</td>
                                            <td>{{ ucfirst($med->route ?? '-') }}</td>
                                            <td>{{ $med->dose ?? '-' }} {{ $med->dose_unit ?? '' }}</td>
                                            <td>{{ $med->formatted_frequency ?? '-' }}</td>
                                            <td>{{ $med->duration_value ?? '-' }} {{ ucfirst($med->duration_unit ?? '') }}</td>
                                            <td>{{ $med->instructions ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('visit.destroyMedication', $med->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No medications prescribed for this visit</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Drug History Section -->
                                <h5 class="fw-bold mb-3 mt-4">Drug History</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 25%;">Drug Name</th>
                                            <th style="width: 15%;">Dose</th>
                                            <th style="width: 15%;">Frequency</th>
                                            <th style="width: 20%;">For</th>
                                            <th style="width: 12%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($drugHistories as $index => $drug)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $drug->drug_name ?? '-' }}</td>
                                            <td>{{ $drug->drug_dose ?? '-' }} {{ $drug->dose_unit ?? '' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $drug->drug_frequency ?? '-')) }}</td>
                                            <td>{{ $drug->drug_for ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $drug->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $drug->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No drug history records</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Observations Tab -->
    <div @class(['tab-pane', 'fade']) id="observations" role="tabpanel" aria-labelledby="observations-tab">
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light'])>
                <h6 @class(['mb-0'])>Examination Details</h6>
            </div>
            <div @class(['card-body'])>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the errors below:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('visit.storeExamination') }}">
                    @csrf
                    <div @class(['row'])>
                        <input type="text" @class(['form-control']) name="patient_id" value="{{ $patient->id }}" hidden>
                    
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>Primary Symptom</label>
                        <input type="text" @class(['form-control']) name="primary_symptom" value="{{ $examination->primary_symptom ?? '' }}" placeholder="Enter primary symptom...">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>For how long?</label>
                        <input type="text" @class(['form-control']) name="symptom_duration_value" value="{{ $examination->symptom_duration_value ?? '' }}" placeholder="Duration of symptoms...">
                    </div>
                    </div>
                    <div @class(['row'])>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>Duration of Symptoms</label>
                        <select @class(['form-select']) name="symptom_duration_unit">
                            <option value="">Select duration</option>
                            <option value="days" {{ ($examination->symptom_duration_unit ?? '') == 'days' ? 'selected' : '' }}>Days</option>
                            <option value="weeks" {{ ($examination->symptom_duration_unit ?? '') == 'weeks' ? 'selected' : '' }}>Weeks</option>
                            <option value="months" {{ ($examination->symptom_duration_unit ?? '') == 'months' ? 'selected' : '' }}>Months</option>
                            <option value="years" {{ ($examination->symptom_duration_unit ?? '') == 'years' ? 'selected' : '' }}>Years</option>
                        </select>
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>Other Symptoms</label>
                        <input type="text" @class(['form-control']) name="other_symptoms" value="{{ $examination->other_symptoms ?? '' }}" placeholder="Enter other symptoms if any...">   
                    </div>
                    </div>
                <button type="submit" @class(['btn', 'btn-primary', 'btn-sm'])>
                    <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Save 
                </button>
                </form>
            </div>
        </div>

        <!-- Vitals and Visit Details Row -->
<div @class(['row', 'mt-4'])>
    <!-- Vitals Card -->
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light'])>
                <h6 @class(['mb-0'])>
                    <i @class(['bi', 'bi-heart-pulse', 'me-2'])></i>Vital Signs
                </h6>
            </div>
            <form method="POST" action="{{ route('visit.storeVitals') }}">
            @csrf
            <input type="text" @class(['form-control']) name="patient_id" value="{{ $patient->id }}" hidden>
            <div @class(['card-body'])>
                <div @class(['row'])>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Blood Pressure</small>
                        <input name="blood_pressure" type="text" @class(['form-control']) value="{{ $vitals->blood_pressure ?? '' }}" placeholder="e.g., 120/80 mmHg">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Pulse</small>
                        <input name="heart_rate" type="text" @class(['form-control']) value="{{ $vitals->heart_rate ?? '' }}" placeholder="e.g., 72 bpm">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Temperature</small>
                        <input name="temperature" type="text" @class(['form-control']) value="{{ $vitals->temperature ?? '' }}" placeholder="e.g., 98.6°F">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Respiratory Rate</small>
                        <input name="respiratory_rate" type="text" @class(['form-control']) value="{{ $vitals->respiratory_rate ?? '' }}" placeholder="e.g., 16 breaths/min">
                    </div>
                    <div @class(['col-md-6'])>
                        <small @class(['text-muted', 'd-block'])>Height</small>
                        <input type="text" name="height" @class(['form-control']) value="{{ $vitals->height ?? '' }}" placeholder="e.g., 5'10\"">
                    </div>
                    <div @class(['col-md-6'])>
                        <small @class(['text-muted', 'd-block'])>Weight</small>
                        <input type="text" name="weight" @class(['form-control']) value="{{ $vitals->weight ?? '' }}" placeholder="e.g., 70 kg">
                    </div>
                </div>
                <button @class(['btn', 'btn-sm', 'btn-outline-primary', 'mt-3'])>
                    <i @class(['bi', 'bi-pencil', 'me-1'])></i>Save Vitals
                </button>
            </div>
            </form>
        </div>
    </div>

    <div @class(['row', 'mt-4'])>
    <!-- Medical History Card - Disease & Drug History -->
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light'])>
                <h6 @class(['mb-0'])>
                    <i @class(['bi', 'bi-file-earmark-medical', 'me-2'])></i>Patient History
                </h6>
            </div>
            <div @class(['card-body'])>
                <form id="medicalHistoryForm" method="POST" action="{{ route('visit.storeDiseaseHistory') }}">
                    @csrf
                    <input type="text" name="patient_id" value="{{ $patient->id }}" hidden>
               
                    <!-- Existing Disease History Display -->
                    @if($diseaseHistories->isNotEmpty())
                    <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Existing Disease History</h6>
                        <table @class(['table', 'table-bordered', 'table-sm'])>
                            <thead @class(['bg-light'])>
                                <tr>
                                    <th style="width: 5%;">S.N</th>
                                    <th style="width: 30%;">Name</th>
                                    <th style="width: 15%;">Duration</th>
                                    <th style="width: 15%;">Unit</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($diseaseHistories as $index => $disease)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $disease->name }}</td>
                                    <td>{{ $disease->duration_value ?? '-' }}</td>
                                    <td>{{ ucfirst($disease->duration_unit ?? '-') }}</td>
                                    <td>
                                        <span class="badge {{ $disease->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $disease->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Existing Drug History Display -->
                    @if($drugHistories->isNotEmpty())
                    <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Existing Drug History</h6>
                        <table @class(['table', 'table-bordered', 'table-sm'])>
                            <thead @class(['bg-light'])>
                                <tr>
                                    <th style="width: 5%;">S.N</th>
                                    <th style="width: 20%;">Drug Name</th>
                                    <th style="width: 15%;">Dose</th>
                                    <th style="width: 15%;">Frequency</th>
                                    <th style="width: 20%;">For</th>
                                    <th style="width: 10%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($drugHistories as $index => $drug)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $drug->drug_name ?? '-' }}</td>
                                    <td>{{ $drug->drug_dose ?? '-' }} {{ $drug->dose_unit ?? '' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $drug->drug_frequency ?? '-')) }}</td>
                                    <td>{{ $drug->drug_for ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $drug->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $drug->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <hr class="my-4">

                    <!-- Disease History Section -->
                    <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Add New Disease History</h6>
                        <table @class(['table', 'table-bordered', 'table-sm'])>
                            <thead @class(['bg-light'])>
                                <tr>
                                    <th style="width: 35%;">Name <span @class(['text-danger'])>*</span></th>
                                    <th style="width: 20%;">Duration <span @class(['text-danger'])>*</span></th>
                                    <th style="width: 35%;">For <span @class(['text-danger'])>*</span></th>
                                    <th style="width: 20%;">State <span @class(['text-danger'])>*</span></th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody id="diseaseHistoryBody2">
                                <tr>
                                    <td><input type="text" @class(['form-control', 'form-control-sm']) name="name[]" placeholder="Disease name" required></td>
                                    <td><input type="text" @class(['form-control', 'form-control-sm']) name="duration_value[]" placeholder="Duration" required></td>
                                    <td>
                                        <select @class(['form-select', 'form-select-sm']) name="duration_unit[]">
                                            <option value="">Select unit</option>
                                            <option value="days">Days</option>
                                            <option value="weeks">Weeks</option>
                                            <option value="months">Months</option>
                                            <option value="years">Years</option>
                                        </select>
                                    </td>
                                     
                                    <td><input type="checkbox" @class(['form-check-input']) name="status[]" value="1" checked></td>
                                    <td @class(['text-center'])>
                                        <button type="button" @class(['btn', 'btn-sm', 'btn-danger', 'removeRow2']) style="display:none;">
                                            <i @class(['bi', 'bi-x'])></i>
                                        </button>
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                        <button type="button" @class(['btn', 'btn-primary', 'btn-sm']) id="addDiseaseRow2">
                            <i @class(['bi', 'bi-plus-lg', 'me-1'])></i>
                        </button>
                   
                        
                    </div>

                    <!-- Drug History Section -->
                    <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Add New Drug History</h6>
                        <table @class(['table', 'table-bordered', 'table-sm'])>
                            <thead @class(['bg-light'])>
                                <tr>
                                    <th style="width: 25%;">Name</th>
                                    <th style="width: 20%;">Dose</th>
                                    <th style="width: 20%;">Frequency</th>
                                    <th style="width: 20%;">For</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody id="drugHistoryBody2">
                                <tr>
                                    <td><input type="text" @class(['form-control', 'form-control-sm']) name="drug_name[]" placeholder="Drug name"></td>
                                    <td><input type="text" @class(['form-control', 'form-control-sm']) name="drug_dose[]" placeholder="Dose"></td>
                                    <td>
                                        <select @class(['form-select', 'form-select-sm']) name="dose_unit[]">
                                            <option value="">Select unit</option>
                                            <option value="mg">mg</option>
                                            <option value="g">g</option>
                                            <option value="ml">ml</option>
                                            <option value="units">units</option>
                                        </select>
                                    <td>
                                        <select @class(['form-select', 'form-select-sm']) name="drug_frequency[]">
                                            <option value="">Select</option>
                                            <option value="once_daily">Once Daily</option>
                                            <option value="twice_daily">Twice Daily</option>
                                            <option value="thrice_daily">Thrice Daily</option>
                                            <option value="as_needed">As Needed</option>
                                        </select>
                                    </td>
                                    <td><input type="text" @class(['form-control', 'form-control-sm']) name="drug_for[]" placeholder="Condition"></td>
                                    <td><input type="checkbox" @class(['form-check-input']) name="drug_status[]" value="1" checked></td>
                                    <td @class(['text-center'])>
                                        <button type="button" @class(['btn', 'btn-sm', 'btn-danger', 'removeRow2']) style="display:none;">
                                            <i @class(['bi', 'bi-x'])></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" @class(['btn', 'btn-primary', 'btn-sm']) id="addDrugRow2">
                            <i @class(['bi', 'bi-plus-lg', 'me-1'])></i>
                        </button>
                    </div>

                    <!-- Action Button -->
                    <button type="submit" @class(['btn', 'btn-success', 'btn-sm'])>
                        <i @class(['bi', 'bi-check-circle', 'me-1'])></i>Save
                    </button>
                </form>
            </div>
    </div>
</div>

    </div>

    <!-- Lab Orders Tab -->
    <div @class(['tab-pane', 'fade']) id="laborders" role="tabpanel" aria-labelledby="laborders-tab">
        <div @class(['card', 'shadow-sm'])>
            
            <div @class(['card-body'])>
                <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Lab Orders</h6>
                        <table @class(['table', 'table-bordered', 'table-sm'])>
                            <thead @class(['bg-light'])>
                                <tr>
                                    <th style="width: 10%;">SN</th>
                                    <th style="width: 50%;">Test Service<span @class(['text-danger'])>*</span></th>
                                    <th style="width: 30%;">Service Code <span @class(['text-danger'])>*</span></th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <form id="labOrderForm" method="POST" action="{{ route('visit.storeLabOrders') }}">
                                
                                @csrf
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Please fix the errors below:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <input type="hidden" name="appointment_id" value="{{ $currentAppointment->id ?? '' }}">
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>1</td>
                                    <td class="position-relative">
                                        <input type="text" class="form-control form-control-sm service-search" placeholder="Type to search..." autocomplete="off">
                                        <input type="hidden" name="lab_orders[0][service_id]" class="service-id">
                                        <input type="hidden" name="lab_orders[0][service_type]" class="service-type">
                                        <div class="service-results position-absolute w-100 bg-white border rounded shadow-sm" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm" name="lab_orders[0][service_code]" placeholder="Service code" readonly></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger removeRow2" style="display:none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            
                        </table>
                        <button type="button" id="addLaborderRow2" class="btn btn-sm btn-primary">Add Service</button>
                        <button type="submit" class="btn btn-success btn-sm ms-2">
                            <i class="bi bi-check-circle me-1"></i>Save Lab Orders
                        </button>
                   </form>
                        
                    </div>
            </div>
        </div>
    </div>

    <!-- Diagnosis Tab -->
    <div @class(['tab-pane', 'fade']) id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light', 'd-flex', 'justify-content-between', 'align-items-center'])>
                <h6 @class(['mb-0'])>Add Diagnosis</h6>
            </div>
            <div @class(['card-body'])>
                <form method="POST" action="{{ route('visit.storeDiagnosis') }}">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $currentAppointment->id ?? '' }}">
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label @class(['form-label'])>Primary Diagnosis <span class="text-danger">*</span></label>
                            <input type="text" @class(['form-control']) name="primary_diagnosis" placeholder="Enter primary diagnosis..." required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label @class(['form-label'])>Secondary Diagnosis</label>
                            <input type="text" @class(['form-control']) name="secondary_diagnosis" placeholder="Enter secondary diagnosis (optional)...">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label @class(['form-label'])>Follow-up (Days)</label>
                            <input type="number" @class(['form-control']) name="follow_up_days" placeholder="e.g., 7" min="1">
                        </div>
                    </div>
                    <button type="submit" @class(['btn', 'btn-primary', 'btn-sm'])>
                        <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Save Diagnosis
                    </button>
                </form>
                
                <!-- Existing Diagnoses -->
                @if($diagnoses->isNotEmpty())
                <hr class="my-4">
                <h6 class="fw-bold mb-3">Current Visit Diagnoses</h6>
                <table class="table table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>S.N</th>
                            <th>Primary Diagnosis</th>
                            <th>Secondary Diagnosis</th>
                            <th>Follow-up (Days)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnoses as $index => $diag)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $diag->primary_diagnosis }}</td>
                            <td>{{ $diag->secondary_diagnosis ?? '-' }}</td>
                            <td>{{ $diag->follow_up_days ?? '-' }}</td>
                            <td>
                                <form action="{{ route('visit.destroyDiagnosis', $diag->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this diagnosis?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Medication Tab -->
    <div @class(['tab-pane', 'fade']) id="medication" role="tabpanel" aria-labelledby="medication-tab">
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light', 'd-flex', 'justify-content-between', 'align-items-center'])>
                <h6 @class(['mb-0'])>Prescribe Medication</h6>
            </div>
            <div @class(['card-body'])>
                <form action="{{ route('visit.storeMedication') }}" method="POST">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $currentAppointment->id ?? '' }}">
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Drug Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="drug_name" placeholder="e.g., Aspirin" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Route <span class="text-danger">*</span></label>
                            <select class="form-select" name="route" required>
                                <option value="">Select route</option>
                                <option value="oral">Oral</option>
                                <option value="intramuscular">Intramuscular</option>
                                <option value="intravenous">Intravenous</option>
                                <option value="subcutaneous">Subcutaneous</option>
                                <option value="topical">Topical</option>
                                <option value="inhalation">Inhalation</option>
                                <option value="rectal">Rectal</option>
                                <option value="sublingual">Sublingual</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Dose <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="dose" placeholder="500" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Units <span class="text-danger">*</span></label>
                            <select class="form-select" name="dose_unit" required>
                                <option value="">Select</option>
                                <option value="mg">mg</option>
                                <option value="g">g</option>
                                <option value="ml">ml</option>
                                <option value="mcg">mcg</option>
                                <option value="tablet">Tablet</option>
                                <option value="capsule">Capsule</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" name="frequency" required>
                                <option value="">Select</option>
                                <option value="once_daily">Once a day</option>
                                <option value="twice_daily">Twice a day</option>
                                <option value="thrice_daily">Thrice a day</option>
                                <option value="four_times_daily">Four times a day</option>
                                <option value="every_4_hours">Every 4 hours</option>
                                <option value="every_6_hours">Every 6 hours</option>
                                <option value="every_8_hours">Every 8 hours</option>
                                <option value="every_12_hours">Every 12 hours</option>
                                <option value="weekly">Weekly</option>
                                <option value="as_needed">As needed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="number" class="form-control" name="duration_value" placeholder="7">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Duration Unit</label>
                            <select class="form-select" name="duration_unit">
                                <option value="">Select</option>
                                <option value="days">Days</option>
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Instructions</label>
                            <textarea class="form-control" name="instructions" rows="1" placeholder="e.g., Take with food"></textarea>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i> Add
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Current Visit Medications -->
                <h6 class="mt-4 mb-3">Current Prescriptions</h6>
                @if(isset($medications) && $medications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Drug Name</th>
                                    <th>Route</th>
                                    <th>Dose</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medications as $index => $medication)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $medication->drug_name }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($medication->route) }}</span></td>
                                        <td>{{ $medication->dose }} {{ $medication->dose_unit }}</td>
                                        <td>{{ $medication->formatted_frequency ?? str_replace('_', ' ', ucfirst($medication->frequency)) }}</td>
                                        <td>{{ $medication->duration_value ? $medication->duration_value . ' ' . $medication->duration_unit : '-' }}</td>
                                        <td>{{ $medication->instructions ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('visit.destroyMedication', $medication->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this medication?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>No medications prescribed for this visit yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsBody = document.getElementById('itemsBody');
        let serviceSearchTimeout;

        // Add Row Functionality
        const addRowBtn = document.getElementById('addLaborderRow2');
        if (addRowBtn && itemsBody) {
            addRowBtn.addEventListener('click', function() {
                const rowCount = itemsBody.querySelectorAll('.item-row').length;
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
                    <td>${rowCount + 1}</td>
                    <td class="position-relative">
                        <input type="text" class="form-control form-control-sm service-search" placeholder="Type to search..." autocomplete="off">
                        <input type="hidden" name="lab_orders[${rowCount}][service_id]" class="service-id">
                        <input type="hidden" name="lab_orders[${rowCount}][service_type]" class="service-type">
                        <div class="service-results position-absolute w-100 bg-white border rounded shadow-sm" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="lab_orders[${rowCount}][service_code]" placeholder="Service code" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger removeRow2">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                `;
                itemsBody.appendChild(newRow);
                updateRemoveButtons();
            });
        }

        // Remove Row Functionality
        function updateRemoveButtons() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach((row, idx) => {
                const btn = row.querySelector('.removeRow2');
                if (btn) {
                    btn.style.display = rows.length > 1 ? '' : 'none';
                    btn.onclick = function() {
                        row.remove();
                        updateRowIndexes();
                    };
                }
            });
        }
        function updateRowIndexes() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach((row, idx) => {
                row.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/lab_orders\[\d+\]/, `lab_orders[${idx}]`);
                    }
                });
                row.querySelector('td').textContent = idx + 1;
            });
            updateRemoveButtons();
        }
        updateRemoveButtons();

        // Service Search Logic (unchanged)
        if (itemsBody) {
            itemsBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('service-search')) {
                    const input = e.target;
                    const row = input.closest('.item-row');
                    const resultsDiv = row.querySelector('.service-results');
                    clearTimeout(serviceSearchTimeout);
                    const query = input.value.trim();
                    if (query.length < 2) {
                        resultsDiv.style.display = 'none';
                        return;
                    }
                    serviceSearchTimeout = setTimeout(() => {
                        fetch(`{{ route('bills.serviceSearch') }}?query=${encodeURIComponent(query)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(services => {
                            if (services.length === 0) {
                                resultsDiv.innerHTML = '<div class="p-3 text-muted">No services found</div>';
                            } else {
                                resultsDiv.innerHTML = services.map(service => `
                                    <div class="service-item p-2 border-bottom" style="cursor: pointer;" 
                                        data-id="${service.id}" 
                                        data-name="${service.name}" 
                                        data-code="${service.code || ''}" 
                                        data-price="${service.price || 0}"
                                        data-type="${service.type}">
                                        <div class="fw-semibold">${service.name}</div>
                                        <small class="text-muted">${service.code || ''} | Rs. ${parseFloat(service.price || 0).toFixed(2)}</small>
                                    </div>
                                `).join('');
                            }
                            resultsDiv.style.display = 'block';
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            resultsDiv.innerHTML = '<div class="p-3 text-danger">Error searching services</div>';
                            resultsDiv.style.display = 'block';
                        });
                    }, 300);
                }
            });

            itemsBody.addEventListener('click', function(e) {
                const item = e.target.closest('.service-item');
                if (item) {
                    const row = item.closest('.item-row');
                    const serviceId = row.querySelector('.service-id');
                    const serviceType = row.querySelector('.service-type');
                    const serviceInput = row.querySelector('.service-search');

                    // Populate service_id and service_type with valid values
                    serviceId.value = item.dataset.id;
                    serviceType.value = item.dataset.type; // Use the type directly from API
                    serviceInput.value = item.dataset.name;

                    // Hide the results dropdown
                    row.querySelector('.service-results').style.display = 'none';

                    // Ensure service_id is valid and matches the dataset
                    if (!serviceId.value) {
                        alert('Invalid service selected. Please try again.');
                        return;
                    }
                }
            });

            itemsBody.addEventListener('mouseover', function(e) {
                const item = e.target.closest('.service-item');
                if (item) item.style.backgroundColor = '#f8f9fa';
            });
            itemsBody.addEventListener('mouseout', function(e) {
                const item = e.target.closest('.service-item');
                if (item) item.style.backgroundColor = '';
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.item-row')) {
                document.querySelectorAll('.service-results').forEach(div => {
                    div.style.display = 'none';
                });
            }
        });
    });
</script>
@endpush

<!-- Visit History Modal -->
<div class="modal fade" id="visitHistoryModal" tabindex="-1" aria-labelledby="visitHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="visitHistoryModalLabel">
                    <i class="bi bi-clock-history me-2"></i>Visit History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Completed visits for <strong>{{ $patient->full_name }}</strong></p>
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 8%;">S.N</th>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 15%;">Time</th>
                            <th style="width: 25%;">Doctor</th>
                            <th style="width: 22%;">Reason</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $completedAppointments = $patient->appointments->where('status', 'completed');
                        @endphp
                        @forelse($completedAppointments as $index => $appointment)
                            <tr class="{{ optional($currentAppointment)->id == $appointment->id ? 'table-primary' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
                                <td>{{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->reason ?? '-' }}</td>
                                <td>
                                    @if(optional($currentAppointment)->id == $appointment->id)
                                        <span class="badge bg-primary">Viewing</span>
                                    @else
                                        <a href="{{ route('patients.visitDetails', $patient->id) }}?appointment_id={{ $appointment->id }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                    No completed visits found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                @if(optional($currentAppointment)->status == 'completed')
                    <a href="{{ route('patients.visitDetails', $patient->id) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>Go to Current Visit
                    </a>
                @endif
                <a href="{{ route('appointments.create', $patient->id) }}" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i>New Appointment
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Visit History Button Click Handler
    document.getElementById('visitHistoryBtn').addEventListener('click', function() {
        var visitHistoryModal = new bootstrap.Modal(document.getElementById('visitHistoryModal'));
        visitHistoryModal.show();
    });

</script>

@endsection