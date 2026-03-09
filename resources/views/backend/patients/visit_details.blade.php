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
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Pulse</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Height</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Weight</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Temperature</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Respiratory rate</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Blood oxygen saturation</span>
                            <span class="text-muted">-</span>
                        </div>
                    </div>
                </div>

                <!-- Examinations Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Examinations</h5>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">Primary symptoms</span>
                            <span class="text-muted">-</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-primary fw-semibold">For</span>
                            <span class="text-muted">-</span>
                        </div>
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
                                            <th style="width: 25%;">Diagnosis</th>
                                            <th style="width: 15%;">Order</th>
                                            <th style="width: 15%;">Certainty</th>
                                            <th style="width: 20%;">Remarks</th>
                                            <th style="width: 12%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No diagnosis records</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Lab Results Tab -->
                            <div class="tab-pane fade" id="lab-results" role="tabpanel">
                                <h5 class="fw-bold mb-3">Lab Results</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 30%;">Test Name</th>
                                            <th style="width: 20%;">Service Category</th>
                                            <th style="width: 20%;">Service Type</th>
                                            <th style="width: 12%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No lab results</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Medication Tab -->
                            <div class="tab-pane fade" id="medication-details" role="tabpanel">
                                <h5 class="fw-bold mb-3">Medication</h5>
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 8%;">S.N</th>
                                            <th style="width: 25%;">Drug Name</th>
                                            <th style="width: 15%;">Dose</th>
                                            <th style="width: 15%;">Frequency</th>
                                            <th style="width: 20%;">Instructions</th>
                                            <th style="width: 12%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No medication records</td>
                                        </tr>
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
                        <input type="text" @class(['form-control']) name="primary_symptom" placeholder="Enter primary symptom...">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>For how long?</label>
                        <input type="text" @class(['form-control']) name="symptom_duration_value" placeholder="Duration of symptoms...">
                    </div>
                    </div>
                    <div @class(['row'])>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>Duration of Symptoms</label>
                        <select @class(['form-select']) name="symptom_duration_unit">
                            <option value="">Select duration</option>
                            <option value="days">Days</option>
                            <option value="weeks">Weeks</option>
                            <option value="months">Months</option>
                            <option value="years">Years</option>
                        </select>
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <label @class(['form-label'])>Other Symptoms</label>
                        <input type="text" @class(['form-control']) name="other_symptoms" placeholder="Enter other symptoms if any...">   
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
                        <input name="blood_pressure" type="text" @class(['form-control']) value="{{ $patient->observationVitals->blood_pressure ?? '120/80 mmHg' }}">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Pulse</small>
                        <input name="heart_rate" type="text" @class(['form-control']) value="{{ $patient->observationVitals->heart_rate ?? '72 bpm' }}">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Temperature</small>
                        <input name="temperature" type="text" @class(['form-control']) value="{{ $patient->observationVitals->temperature ?? '98.6°F' }}">
                    </div>
                    <div @class(['col-md-6', 'mb-3'])>
                        <small @class(['text-muted', 'd-block'])>Respiratory Rate</small>
                        <input name="respiratory_rate" type="text" @class(['form-control']) value="{{ $patient->observationVitals->respiratory_rate ?? '16 breaths/min' }}">
                    </div>
                    <div @class(['col-md-6'])>
                        <small @class(['text-muted', 'd-block'])>Height</small>
                        <input type="text" name="height" @class(['form-control']) value="{{ $patient->observationVitals->height ?? '5\'10"' }}">
                    </div>
                    <div @class(['col-md-6'])>
                        <small @class(['text-muted', 'd-block'])>Weight</small>
                        <input type="text" name="weight" @class(['form-control']) value="{{ $patient->observationVitals->weight ?? '70 kg' }}">
                    </div>
                </div>
                <button @class(['btn', 'btn-sm', 'btn-outline-primary', 'mt-3'])>
                    <i @class(['bi', 'bi-pencil', 'me-1'])></i>Edit Vitals
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
               
                    <!-- Disease History Section -->
                    <div @class(['mb-4'])>
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Disease History</h6>
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
                        <h6 @class(['fw-bold', 'mb-3', 'text-primary'])>Drug History</h6>
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
                <h6 @class(['mb-0'])>Diagnosis</h6>
            </div>
            <div @class(['card-body'])>
                <div @class(['form-group'])>
                    <label @class(['form-label'])>Primary Diagnosis</label>
                    <input type="text" @class(['form-control']) placeholder="Enter primary diagnosis...">
                </div>
                <div @class(['form-group', 'mt-3'])>
                    <label @class(['form-label'])>Secondary Diagnosis</label>
                    <textarea @class(['form-control']) rows="3" placeholder="Enter secondary diagnoses if any..."></textarea>
                </div>
                <div @class(['form-group', 'mt-3'])>
                    <label @class(['form-label'])>Follow Up</label>
                    <input type="number" @class(['form-control']) placeholder="Enter follow up after... days">
                </div>
            </div>
            <button @class(['btn', 'btn-primary', 'btn-sm', 'mt-3'])>
                <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Save Diagnosis
            </button>
        </div>
    </div>

    <!-- Medication Tab -->
    <div @class(['tab-pane', 'fade']) id="medication" role="tabpanel" aria-labelledby="medication-tab">
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-header', 'bg-light', 'd-flex', 'justify-content-between', 'align-items-center'])>
                <h6 @class(['mb-0'])>Medication</h6>
            </div>
            <div @class(['card-body'])>
                <div id="medicationContainer">
                    <form id="medicationForm" method="POST" >
                        @csrf
                        <input type="text" name="patient_id" value="{{ $patient->id }}" hidden>
                        <div class="medication-item mb-3 p-3 border rounded position-relative">
                            <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Drug Name</label>
                            <input type="text" class="form-control" placeholder="e.g., Aspirin">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Route</label>
                            <select class="form-select">
                                <option value="">Select route</option>
                                <option value="oral">Oral</option>
                                <option value="intramuscular">Intramuscular</option>
                                <option value="intravenous">Intravenous</option>
                                <option value="subcutaneous">Subcutaneous</option>
                                <option value="topical">Topical</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Dose</label>
                            <input type="text" class="form-control" placeholder="500">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Units</label>
                            <select class="form-select">
                                <option value="">Select</option>
                                <option value="mg">mg</option>
                                <option value="g">g</option>
                                <option value="ml">ml</option>
                                <option value="capsule">Capsule</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Frequency</label>
                            <select class="form-select">
                                <option value="">Select</option>
                                <option value="once_daily">Once a day</option>
                                <option value="twice_daily">Twice a day</option>
                                <option value="thrice_daily">Thrice a day</option>
                                <option value="as_needed">As needed</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="number" class="form-control" placeholder="7">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Unit</label>
                            <select class="form-select">
                                <option value="">Select</option>
                                <option value="days">Days</option>
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold">Instructions</label>
                            <textarea class="form-control" rows="1" placeholder="Take with food"></textarea>
                        </div>
                    </div>
                        </div>
                        <!-- Add more medication items dynamically -->
                        <!-- End of dynamic medication items -->
                    </form>
                </div>
                <button type="button" @class(['btn', 'btn-primary', 'btn-sm']) id="addMedBtn">
                    <i @class(['bi', 'bi-plus-lg', 'me-1'])></i>
                </button>
                <button type="button" @class(['btn', 'btn-success', 'btn-sm', 'ms-2']) id="saveMedBtn">
                    <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Save Medications
                </button>
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
                                        data-price="${service.price || 0}">
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
                    serviceType.value = item.dataset.type === 'LabTest' ? 'App\\Models\\LabTest' : 'App\\Models\\LabGroup';
                    serviceInput.value = item.dataset.name;

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