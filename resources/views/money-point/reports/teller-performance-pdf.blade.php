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
        .positive {
            color: #28a745;
        }
        .negative {
            color: #dc3545;
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
            </p>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Teller</th>
                <th class="text-center">Total Shifts</th>
                <th class="text-center">Verified</th>
                <th class="text-center">Discrepancies</th>
                <th class="text-center">Completion Rate</th>
                <th class="text-center">Transactions</th>
                <th class="text-right">Total Deposits</th>
                <th class="text-right">Total Withdrawals</th>
                <th class="text-right">Net Flow</th>
            </tr>
        </thead>
        <tbody>
            @if(empty($performance))
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">No performance data found for the selected criteria.</td>
                </tr>
            @else
                @foreach($performance as $perf)
                    @php
                        $completionRate = $perf['shifts_count'] > 0 
                            ? ($perf['shifts_verified'] / $perf['shifts_count']) * 100 
                            : 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $perf['teller']->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $perf['shifts_count'] }}</td>
                        <td class="text-center">{{ $perf['shifts_verified'] }}</td>
                        <td class="text-center">{{ $perf['shifts_discrepancy'] }}</td>
                        <td class="text-center">{{ number_format($completionRate, 1) }}%</td>
                        <td class="text-center">{{ $perf['transactions_count'] }}</td>
                        <td class="text-right">{{ number_format($perf['total_deposits'], 0) }}</td>
                        <td class="text-right">{{ number_format($perf['total_withdrawals'], 0) }}</td>
                        <td class="text-right {{ $perf['net_flow'] >= 0 ? 'positive' : 'negative' }}">
                            {{ number_format($perf['net_flow'], 0) }}
                        </td>
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

