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
            font-size: 11px;
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
            padding: 5px;
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
                @if($type)
                    | <strong>Type:</strong> {{ ucfirst($type) }}
                @endif
                @if($teller)
                    | <strong>Teller:</strong> {{ $teller->name }}
                @endif
            </p>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Date/Time</th>
                <th>Type</th>
                <th>Teller</th>
                <th>Shift</th>
                <th class="text-right">Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @if($transactions->isEmpty())
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">No transactions found for the selected criteria.</td>
                </tr>
            @else
                @php
                    $totalAmount = 0;
                @endphp
                @foreach($transactions as $transaction)
                    @php
                        $cashLine = $transaction->lines->firstWhere('account.account_type', 'cash');
                        $amount = $cashLine ? abs($cashLine->amount) : 0;
                        $totalAmount += $amount;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ ucfirst($transaction->type) }}</td>
                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                        <td>
                            @if($transaction->tellerShift)
                                {{ \Carbon\Carbon::parse($transaction->tellerShift->opened_at)->format('Y-m-d') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($amount, 0) }}</td>
                        <td>{{ $transaction->reference ?? 'N/A' }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="5" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($totalAmount, 0) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #666;">
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

