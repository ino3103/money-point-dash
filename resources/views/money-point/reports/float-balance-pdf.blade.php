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
        .provider-section {
            margin-bottom: 30px;
        }
        .provider-header {
            background-color: #0056b3;
            color: white;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
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
                <strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}
                @if($provider)
                    | <strong>Provider:</strong> {{ $provider->display_name }}
                @endif
                @if($teller)
                    | <strong>Teller:</strong> {{ $teller->name }}
                @endif
            </p>
        </div>
    </header>

    @if($groupedAccounts->isEmpty())
        <div class="text-center" style="padding: 20px;">
            <p>No float accounts found for the selected criteria.</p>
        </div>
    @else
        @foreach($groupedAccounts as $providerName => $accounts)
            <div class="provider-section">
                <div class="provider-header">
                    {{ $floatProviders[$providerName]->display_name ?? ucfirst($providerName) }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Teller</th>
                            <th class="text-right">Current Balance</th>
                            <th class="text-right">System Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $providerTotal = 0;
                        @endphp
                        @foreach($accounts as $account)
                            @php
                                $balance = abs($account->balance);
                                $providerTotal += $balance;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $account->user->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($balance, 0) }}</td>
                                <td class="text-right">{{ number_format($account->balance, 0) }}</td>
                                <td>{{ $account->is_active ? 'Active' : 'Inactive' }}</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #e9ecef; font-weight: bold;">
                            <td colspan="2" class="text-right">TOTAL</td>
                            <td class="text-right">{{ number_format($providerTotal, 0) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #666;">
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

