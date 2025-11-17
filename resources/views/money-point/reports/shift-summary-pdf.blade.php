<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report_title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-details {
            color: #333;
            font-size: 14px;
        }
        .report-title {
            color: #0056b3;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-details">
            <h1>{{ $settings['site_name'] ?? 'SACCOS' }}</h1>
            <div style="font-size: 11px; color: #666;">
                <p>Contact: {{ $settings['contact_phone'] ?? 'N/A' }} | Email: {{ $settings['admin_email'] ?? 'N/A' }}</p>
                <p>Address: {{ $settings['address'] ?? 'N/A' }}</p>
            </div>
            <hr style="border: 1px solid #0056b3; margin: 10px 0;">
            <div class="report-title">{{ $report_title }}</div>
            <p>
                <strong>Date Range:</strong> {{ $start_date }} to {{ $end_date }}
                @if($teller)
                    | <strong>Teller:</strong> {{ $teller->name }}
                @endif
                @if($status)
                    | <strong>Status:</strong> {{ ucfirst($status) }}
                @endif
            </p>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Teller</th>
                <th>Treasurer</th>
                <th>Opened At</th>
                <th>Closed At</th>
                <th>Status</th>
                <th class="text-right">Opening Cash</th>
                <th class="text-right">Closing Cash</th>
                <th class="text-right">Variance Cash</th>
                <th class="text-right">Mtaji</th>
                <th class="text-right">Balanced</th>
            </tr>
        </thead>
        <tbody>
            @if($shifts->isEmpty())
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">No shifts found for the selected criteria.</td>
                </tr>
            @else
                @foreach($shifts as $shift)
                    @php
                        $openingFloats = $shift->opening_floats ?? [];
                        $closingFloats = $shift->closing_floats ?? [];
                        $mtaji = $shift->opening_cash + array_sum(array_map('abs', $openingFloats));
                        $balanced = ($shift->closing_cash ?? 0) + array_sum(array_map('abs', $closingFloats ?? []));
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shift->teller->name ?? 'N/A' }}</td>
                        <td>{{ $shift->treasurer->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->opened_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $shift->closed_at ? \Carbon\Carbon::parse($shift->closed_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td>{{ ucfirst($shift->status) }}</td>
                        <td class="text-right">{{ number_format($shift->opening_cash, 0) }}</td>
                        <td class="text-right">{{ $shift->closing_cash ? number_format($shift->closing_cash, 0) : 'N/A' }}</td>
                        <td class="text-right">{{ $shift->variance_cash ? number_format($shift->variance_cash, 0) : 'N/A' }}</td>
                        <td class="text-right">{{ number_format($mtaji, 0) }}</td>
                        <td class="text-right">{{ $shift->closed_at ? number_format($balanced, 0) : 'N/A' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #666;">
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

