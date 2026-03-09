<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Report - {{ optional($labOrder->appointment->patient)->full_name ?? 'Patient' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #1bb6b1;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1bb6b1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1bb6b1;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .patient-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .patient-info .col {
            flex: 1;
        }
        .patient-info label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
        }
        .patient-info p {
            font-size: 13px;
            margin-top: 2px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th {
            background: #1bb6b1;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .results-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .results-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-normal { color: #28a745; font-weight: bold; }
        .status-high { color: #dc3545; font-weight: bold; }
        .status-low { color: #ffc107; font-weight: bold; }
        .status-critical { color: #dc3545; font-weight: bold; background: #ffeef0; padding: 2px 8px; border-radius: 3px; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-box .line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1bb6b1;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #159e9a;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                padding: 0;
            }
            .report-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Report
    </button>

    <div class="report-container">
        <!-- Header -->
        <div class="header">
            <h1>TeleMed Laboratory</h1>
            <p>Address Line 1, City, Country | Phone: +977-XXX-XXXXXX | Email: lab@telemed.com</p>
            <p style="margin-top: 10px; font-size: 16px; font-weight: bold; color: #333;">LABORATORY REPORT</p>
        </div>

        <!-- Patient Information -->
        <div class="patient-info">
            <div class="col">
                <label>Patient Name</label>
                <p>{{ optional($labOrder->appointment->patient)->full_name ?? 'N/A' }}</p>
                
                <label style="margin-top: 10px; display: block;">Age / Gender</label>
                <p>{{ optional($labOrder->appointment->patient)->age ?? 'N/A' }} Years / {{ optional($labOrder->appointment->patient)->gender ?? 'N/A' }}</p>
            </div>
            <div class="col">
                <label>Sample ID</label>
                <p>{{ $labOrder->sample_id ?? 'SMP-' . $labOrder->id }}</p>
                
                <label style="margin-top: 10px; display: block;">Collection Date</label>
                <p>{{ optional($labOrder->collected_at ?? $labOrder->created_at)->format('d M, Y H:i') ?? 'N/A' }}</p>
            </div>
            <div class="col">
                <label>Report Date</label>
                <p>{{ optional($labOrder->completed_at ?? now())->format('d M, Y H:i') }}</p>
                
                <label style="margin-top: 10px; display: block;">Referred By</label>
                <p>{{ optional(optional($labOrder->appointment->doctor)->user)->name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Results Table -->
        <table class="results-table">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Result</th>
                    <th>Unit</th>
                    <th>Reference Range</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $results = json_decode($labOrder->results ?? '{}', true);
                @endphp
                @forelse($labOrder->services as $service)
                    @php
                        $model = $service->labGroup ?? null;
                        $name = $model->name ?? 'Test';
                        $result = $results[$service->id] ?? [];
                    @endphp
                    <tr>
                        <td><strong>{{ $name }}</strong></td>
                        <td>{{ $result['value'] ?? '-' }}</td>
                        <td>{{ $result['unit'] ?? '-' }}</td>
                        <td>{{ $result['reference'] ?? '-' }}</td>
                        <td>
                            @php $status = $result['status'] ?? 'normal'; @endphp
                            <span class="status-{{ $status }}">{{ ucfirst($status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">No test results available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Remarks -->
        @if($labOrder->remarks)
        <div style="margin-bottom: 20px; padding: 10px; background: #fff3cd; border-radius: 5px;">
            <strong>Remarks:</strong> {{ $labOrder->remarks }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p style="font-size: 10px; color: #666; text-align: center;">
                This is a computer generated report. Please consult your doctor for interpretation of results.
            </p>
            
            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <p><strong>Lab Technician</strong></p>
                    <p>{{ $labOrder->technician ?? 'N/A' }}</p>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <p><strong>Pathologist</strong></p>
                    <p>Dr. _____________</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
