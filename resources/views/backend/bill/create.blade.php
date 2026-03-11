@extends('backend.layout.app')

@section('title')
    <title>Create Bill - TeleMed Admin</title>
@endsection

@section('content')

<div @class(['d-flex', 'justify-content-between', 'align-items-center', 'mb-4'])>
    <div>
        <h4 @class(['mb-1', 'fw-semibold'])>Create Bill</h4>
        <nav aria-label="breadcrumb">
            <ol @class(['breadcrumb', 'mb-0'])>
                <li @class(['breadcrumb-item'])>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li @class(['breadcrumb-item', 'active'])>Bills</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('bills.index') }}" @class(['btn', 'btn-outline-secondary'])>
        <i @class(['bi', 'bi-arrow-left', 'me-2'])></i>Back
    </a>
</div>

<div @class(['row'])>
    <div @class(['col-lg-12'])>
        <div @class(['card', 'shadow-sm'])>
            <div @class(['card-body'])>

                @if($errors->any())
                    <div @class(['alert', 'alert-danger'])>
                        <strong>Please fix the errors below:</strong>
                        <ul @class(['mb-0', 'mt-2'])>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('info'))
                    <div @class(['alert', 'alert-info'])>
                        <i @class(['bi', 'bi-info-circle', 'me-2'])></i>{{ session('info') }}
                    </div>
                @endif

                <form action="{{ route('bills.store') }}" method="POST" id="billForm">
                    @csrf
                    <div @class(['row'])>
                        <!-- Patient Search -->
                        <div @class(['col-md-6', 'mb-3'])>
                            <label @class(['form-label'])>Search Patient <span @class(['text-danger'])>*</span></label>
                            <div @class(['position-relative'])>
                                <input
                                    type="text"
                                    id="patientSearch"
                                    @class(['form-control'])
                                    placeholder="Search by name, phone or email..."
                                    autocomplete="off"
                                    @if(isset($patient))
                                        value="{{ $patient->full_name }}"
                                        readonly
                                    @endif
                                >
                                <input
                                    type="hidden"
                                    name="patient_id"
                                    id="patientId"
                                    value="{{ isset($patient) ? $patient->id : old('patient_id') }}"
                                    required
                                >
                                <input
                                    type="hidden"
                                    name="appointment_id"
                                    value="{{ isset($appointment) ? $appointment->id : old('appointment_id') }}"
                                >
                                <div id="patientResults" @class(['position-absolute', 'w-100', 'bg-white', 'border', 'rounded', 'shadow-sm']) style="display: none; z-index: 1000; max-height: 250px; overflow-y: auto;"></div>
                            </div>
                        </div>
            
                        <div @class(['col-md-6', 'mb-3'])>
                            <label @class(['form-label'])>Bill Date</label>
                            <input type="date" name="bill_date" @class(['form-control']) value="{{ old('bill_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <!-- Patient Details Section -->
                    <div @class(['card', 'bg-light', 'mb-4'])>
                        <div @class(['card-header'])>
                            <h6 @class(['mb-0'])><i @class(['bi', 'bi-person', 'me-2'])></i>Patient Details</h6>
                        </div>
                        <div @class(['card-body'])>
                            <div @class(['row'])>
                                <div @class(['col-md-4', 'mb-3'])>
                                    <label @class(['form-label'])>Name</label>
                                    <input
                                        type="text"
                                        name="patient_name"
                                        @class(['form-control'])
                                        value="{{ isset($patient) ? $patient->full_name : old('patient_name') }}"
                                        readonly
                                    >
                                </div>
                                <div @class(['col-md-4', 'mb-3'])>
                                    <label @class(['form-label'])>Age</label>
                                    <input
                                        type="text"
                                        name="patient_age"
                                        @class(['form-control'])
                                        value="{{ isset($patient) ? $patient->age : old('patient_age') }}"
                                        readonly
                                    >
                                </div>
                                <div @class(['col-md-4', 'mb-3'])>
                                    <label @class(['form-label'])>Gender</label>
                                    <input
                                        type="text"
                                        name="patient_gender"
                                        @class(['form-control'])
                                        value="{{ isset($patient) ? ucfirst($patient->sex) : old('patient_gender') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>

                    <!-- Bill Items Section -->
                    <div @class(['card', 'bg-light', 'mb-4'])>
                        <div @class(['card-header', 'd-flex', 'justify-content-between', 'align-items-center'])>
                            <h6 @class(['mb-0'])><i @class(['bi', 'bi-list-check', 'me-2'])></i>Bill Items</h6>
                            <button type="button" @class(['btn', 'btn-sm', 'btn-primary']) id="addItemBtn">
                                <i @class(['bi', 'bi-plus-lg', 'me-1'])></i>Add Item
                            </button>
                        </div>
                        <div @class(['card-body'])>
                            <div @class(['table-responsive'])>
                                <table @class(['table', 'table-bordered', 'mb-0']) id="itemsTable">
                                    <thead @class(['table-light'])>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 30%;">Service/Item</th>
                                            <th style="width: 10%;">Qty</th>
                                            <th style="width: 15%;">Rate (Rs.)</th>
                                            <th style="width: 15%;">Amount (Rs.)</th>
                                            <th style="width: 15%;">Discount (Rs.)</th>
                                            <th style="width: 15%;">Net (Rs.)</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @if(isset($labOrders) && $labOrders->isNotEmpty())
                                            @php $rowIndex = 0; @endphp
                                            @foreach($labOrders as $index => $labOrder)
                                                @php
                                                    $model = $labOrder->service;
                                                    $name = $model->name ?? 'N/A';
                                                    $price = $model->price ?? $model->charge_amount ?? 0;
                                                    $serviceType = $labOrder->service_type;
                                                    $serviceId = $labOrder->service_id;
                                                    $amount = $price;
                                                    $netAmount = $price;
                                                @endphp
                                                <tr class="item-row">
                                                    <td class="row-number">{{ $index + 1 }}</td>
                                                    <td class="position-relative">
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm service-search"
                                                            value="{{ $name }}"
                                                            readonly
                                                        >
                                                        <input type="hidden" name="items[{{ $rowIndex }}][service_name]" value="{{ $name }}" class="service-name">
                                                        <input type="hidden" name="items[{{ $rowIndex }}][service_type]" value="{{ $serviceType }}" class="service-type">
                                                        <input type="hidden" name="items[{{ $rowIndex }}][service_id]" value="{{ $serviceId }}" class="service-id">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $rowIndex }}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $rowIndex }}][rate]" class="form-control form-control-sm rate-input" step="0.01" min="0" value="{{ $price }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $rowIndex }}][amount]" class="form-control form-control-sm amount-input" step="0.01" value="{{ number_format($amount, 2, '.', '') }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $rowIndex }}][discount]" class="form-control form-control-sm discount-input" step="0.01" min="0" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $rowIndex }}][net_amount]" class="form-control form-control-sm net-input" step="0.01" value="{{ number_format($netAmount, 2, '.', '') }}" readonly>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @php $rowIndex++; @endphp
                                            @endforeach
                                        @else
                                            <tr class="item-row">
                                                <td class="row-number">1</td>
                                                <td class="position-relative">
                                                    <input type="text" class="form-control form-control-sm service-search" placeholder="Type to search..." autocomplete="off">
                                                    <input type="hidden" name="items[0][service_name]" class="service-name">
                                                    <input type="hidden" name="items[0][service_type]" class="service-type">
                                                    <input type="hidden" name="items[0][service_id]" class="service-id">
                                                    <div class="service-results position-absolute w-100 bg-white border rounded shadow-sm" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][rate]" class="form-control form-control-sm rate-input" step="0.01" min="0" value="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][amount]" class="form-control form-control-sm amount-input" step="0.01" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][discount]" class="form-control form-control-sm discount-input" step="0.01" min="0" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][net_amount]" class="form-control form-control-sm net-input" step="0.01" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" disabled>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div @class(['row'])>
                        <div @class(['col-md-6'])>
                            <div @class(['mb-3'])>
                                <label @class(['form-label'])>Notes</label>
                                <textarea name="notes" @class(['form-control']) rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div @class(['col-md-6'])>
                            <div @class(['card', 'bg-light'])>
                                <div @class(['card-body'])>
                                    <h6 @class(['card-title', 'mb-3'])>Bill Summary</h6>
                                    <div @class(['d-flex', 'justify-content-between', 'mb-2'])>
                                        <span>Gross Amount:</span>
                                        <span @class(['fw-semibold'])>Rs. <span id="grossAmount">0.00</span></span>
                                        <input type="hidden" name="gross_amount" id="grossAmountInput" value="0">
                                    </div>
                                    <div @class(['d-flex', 'justify-content-between', 'mb-2'])>
                                        <span>Total Discount:</span>
                                        <span @class(['text-danger'])>- Rs. <span id="totalDiscount">0.00</span></span>
                                        <input type="hidden" name="discount_amount" id="discountAmountInput" value="0">
                                    </div>
                                    <hr>
                                    <div @class(['d-flex', 'justify-content-between'])>
                                        <span @class(['fw-bold'])>Net Amount:</span>
                                        <span @class(['fw-bold', 'text-primary', 'fs-5'])>Rs. <span id="netAmount">0.00</span></span>
                                        <input type="hidden" name="net_amount" id="netAmountInput" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div @class(['mt-4'])>
                        <button type="submit" @class(['btn', 'btn-primary'])>
                            <i @class(['bi', 'bi-check-lg', 'me-1'])></i>Create Bill
                        </button>
                        <a href="{{ route('bills.index') }}" @class(['btn', 'btn-light'])>Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = document.querySelectorAll('.item-row').length || 1;

        // Add new item row
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const tbody = document.getElementById('itemsBody');
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td class="row-number">${itemIndex + 1}</td>
                <td class="position-relative">
                    <input type="text" class="form-control form-control-sm service-search" placeholder="Type to search..." autocomplete="off">
                    <input type="hidden" name="items[${itemIndex}][service_name]" class="service-name">
                    <input type="hidden" name="items[${itemIndex}][service_type]" class="service-type">
                    <input type="hidden" name="items[${itemIndex}][service_id]" class="service-id">
                    <div class="service-results position-absolute w-100 bg-white border rounded shadow-sm" style="display: none; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][rate]" class="form-control form-control-sm rate-input" step="0.01" min="0" value="0" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][amount]" class="form-control form-control-sm amount-input" step="0.01" readonly>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][discount]" class="form-control form-control-sm discount-input" step="0.01" min="0" value="0">
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][net_amount]" class="form-control form-control-sm net-input" step="0.01" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            itemIndex++;
            updateRowNumbers();
            attachRowEvents(newRow);
            updateRemoveButtons();
        });

        // Remove item row
        document.getElementById('itemsBody').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                const row = e.target.closest('.item-row');
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    updateRowNumbers();
                    calculateTotals();
                    updateRemoveButtons();
                }
            }
        });

        // Attach events to initial rows
        document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));

        // Calculate totals on page load for pre-filled items
        calculateTotals();

        function attachRowEvents(row) {
            const qtyInput = row.querySelector('.qty-input');
            const rateInput = row.querySelector('.rate-input');
            const discountInput = row.querySelector('.discount-input');

            [qtyInput, rateInput, discountInput].forEach(input => {
                input.addEventListener('input', function() {
                    calculateRowAmount(row);
                    calculateTotals();
                });
            });
        }

        function calculateRowAmount(row) {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
            
            const amount = qty * rate;
            const netAmount = amount - discount;

            row.querySelector('.amount-input').value = amount.toFixed(2);
            row.querySelector('.net-input').value = netAmount.toFixed(2);
        }

        function calculateTotals() {
            let grossAmount = 0;
            let totalDiscount = 0;
            let netAmount = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                grossAmount += parseFloat(row.querySelector('.amount-input').value) || 0;
                totalDiscount += parseFloat(row.querySelector('.discount-input').value) || 0;
                netAmount += parseFloat(row.querySelector('.net-input').value) || 0;
            });

            document.getElementById('grossAmount').textContent = grossAmount.toFixed(2);
            document.getElementById('totalDiscount').textContent = totalDiscount.toFixed(2);
            document.getElementById('netAmount').textContent = netAmount.toFixed(2);

            document.getElementById('grossAmountInput').value = grossAmount.toFixed(2);
            document.getElementById('discountAmountInput').value = totalDiscount.toFixed(2);
            document.getElementById('netAmountInput').value = netAmount.toFixed(2);
        }

        function updateRowNumbers() {
            document.querySelectorAll('.item-row').forEach((row, index) => {
                row.querySelector('.row-number').textContent = index + 1;
            });
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-item');
                btn.disabled = rows.length <= 1;
            });
        }

        // Patient Search AJAX
        const patientSearch = document.getElementById('patientSearch');
        const patientResults = document.getElementById('patientResults');
        const patientId = document.getElementById('patientId');
        let searchTimeout;

        patientSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                patientResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('bills.patientSearch') }}?query=${encodeURIComponent(query)}`, {
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
                    .then(patients => {
                        if (patients.length === 0) {
                            patientResults.innerHTML = '<div @class(['p-3', 'text-muted'])>No patients found</div>';
                        } else {
                            patientResults.innerHTML = patients.map(patient => `
                                <div @class(['patient-item', 'p-2', 'border-bottom']) style="cursor: pointer;" 
                                    data-id="${patient.id}" 
                                    data-name="${patient.full_name}" 
                                    data-age="${patient.age || ''}" 
                                    data-gender="${patient.sex || ''}"
                                    data-phone="${patient.contact_number || ''}">
                                    <div @class(['fw-semibold'])>${patient.full_name}</div>
                                    <small @class(['text-muted'])>${patient.contact_number || ''} ${patient.email ? '| ' + patient.email : ''}</small>
                                </div>
                            `).join('');
                        }
                        patientResults.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        patientResults.innerHTML = '<div @class(['p-3', 'text-danger'])>Error searching patients</div>';
                        patientResults.style.display = 'block';
                    });
            }, 300);
        });

        // Select patient from results
        patientResults.addEventListener('click', function(e) {
            const item = e.target.closest('.patient-item');
            if (item) {
                const id = item.dataset.id;
                const name = item.dataset.name;
                const age = item.dataset.age;
                const gender = item.dataset.gender;

                patientId.value = id;
                patientSearch.value = name;
                patientResults.style.display = 'none';

                // Update patient details section
                document.querySelector('input[name="patient_name"]').value = name;
                document.querySelector('input[name="patient_age"]').value = age;
                document.querySelector('input[name="patient_gender"]').value = gender;
            }
        });

        // Hide results on click outside
        document.addEventListener('click', function(e) {
            if (!patientSearch.contains(e.target) && !patientResults.contains(e.target)) {
                patientResults.style.display = 'none';
            }
        });

        // Hover effect for patient items
        patientResults.addEventListener('mouseover', function(e) {
            const item = e.target.closest('.patient-item');
            if (item) item.style.backgroundColor = '#f8f9fa';
        });
        patientResults.addEventListener('mouseout', function(e) {
            const item = e.target.closest('.patient-item');
            if (item) item.style.backgroundColor = '';
        });

        // Service Search AJAX (using event delegation for dynamic rows)
        const itemsBody = document.getElementById('itemsBody');
        let serviceSearchTimeout;

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
                            resultsDiv.innerHTML = '<div @class(['p-3', 'text-muted'])>No services found</div>';
                        } else {
                            resultsDiv.innerHTML = services.map(service => `
                                <div @class(['service-item', 'p-2', 'border-bottom']) style="cursor: pointer;" 
                                    data-id="${service.id}" 
                                    data-name="${service.name}" 
                                    data-code="${service.code || ''}" 
                                    data-price="${service.price || 0}"
                                    data-type="${service.type || ''}">
                                    <div @class(['fw-semibold'])>${service.name}</div>
                                    <small @class(['text-muted'])>${service.code || ''} | Rs. ${parseFloat(service.price || 0).toFixed(2)}</small>
                                </div>
                            `).join('');
                        }
                        resultsDiv.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        resultsDiv.innerHTML = '<div @class(['p-3', 'text-danger'])>Error searching services</div>';
                        resultsDiv.style.display = 'block';
                    });
                }, 300);
            }
        });

        // Select service from results
        itemsBody.addEventListener('click', function(e) {
            const item = e.target.closest('.service-item');
            if (item) {
                const row = item.closest('.item-row');
                const serviceInput = row.querySelector('.service-search');
                const serviceName = row.querySelector('.service-name');
                const serviceTypeInput = row.querySelector('.service-type');
                const serviceIdInput = row.querySelector('.service-id');
                const rateInput = row.querySelector('.rate-input');
                const resultsDiv = row.querySelector('.service-results');

                const id = item.dataset.id;
                const name = item.dataset.name;
                const price = parseFloat(item.dataset.price) || 0;
                const type = item.dataset.type || '';

                serviceName.value = name;
                serviceIdInput.value = id;
                serviceTypeInput.value = type;
                serviceInput.value = name;
                rateInput.value = price.toFixed(2);
                resultsDiv.style.display = 'none';

                // Trigger calculation
                calculateRowAmount(row);
                calculateTotals();
            }
        });

        // Hide service results on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.item-row')) {
                document.querySelectorAll('.service-results').forEach(div => {
                    div.style.display = 'none';
                });
            }
        });

        // Hover effect for service items
        itemsBody.addEventListener('mouseover', function(e) {
            const item = e.target.closest('.service-item');
            if (item) item.style.backgroundColor = '#f8f9fa';
        });
        itemsBody.addEventListener('mouseout', function(e) {
            const item = e.target.closest('.service-item');
            if (item) item.style.backgroundColor = '';
        });

    });
        

</script>
@endpush
