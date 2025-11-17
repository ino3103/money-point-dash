<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report_title }}</title>
    <style>
        @page {
            size: A4;
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
        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px;
            background-color: white;
            border-bottom: 1px solid #eee;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .summary-value {
            color: #0056b3;
            font-weight: bold;
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
            <p><strong>Date:</strong> {{ $date }}</p>
        </div>
    </header>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #0056b3;">Transaction Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Deposits:</span>
            <span class="summary-value">{{ number_format($totalDeposits, 0) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Deposit Count:</span>
            <span class="summary-value">{{ $depositCount }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Deposit Size:</span>
            <span class="summary-value">{{ $depositCount > 0 ? number_format($totalDeposits / $depositCount, 0) : 0 }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Withdrawals:</span>
            <span class="summary-value">{{ number_format($totalWithdrawals, 0) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Withdrawal Count:</span>
            <span class="summary-value">{{ $withdrawalCount }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Withdrawal Size:</span>
            <span class="summary-value">{{ $withdrawalCount > 0 ? number_format($totalWithdrawals / $withdrawalCount, 0) : 0 }}</span>
        </div>
        <div class="summary-row" style="background-color: #e9ecef; font-weight: bold;">
            <span class="summary-label">Net Cash Flow:</span>
            <span class="summary-value {{ $netFlow >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($netFlow, 0) }}
            </span>
        </div>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #0056b3;">Shift Statistics</h3>
        <div class="summary-row">
            <span class="summary-label">Shifts Opened:</span>
            <span class="summary-value">{{ $shiftsOpened }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Shifts Closed:</span>
            <span class="summary-value">{{ $shiftsClosed }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Shifts Verified:</span>
            <span class="summary-value">{{ $shiftsVerified }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Verification Rate:</span>
            <span class="summary-value">
                {{ $shiftsClosed > 0 ? number_format(($shiftsVerified / $shiftsClosed) * 100, 1) : 0 }}%
            </span>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #666;">
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>

