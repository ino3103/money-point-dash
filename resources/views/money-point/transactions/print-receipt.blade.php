<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ ucwords($transaction->type) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 0.8cm;
            }

            .receipt-container {
                box-shadow: none !important;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .receipt-container {
            background: white;
            max-width: 600px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
            position: relative;
        }

        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: {{ $transaction->type === 'deposit' ? '#28a745' : '#dc3545' }};
        }

        .receipt-header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
            letter-spacing: 1px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 13px;
            color: #666;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .transaction-badge {
            display: inline-block;
            margin-top: 12px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: {{ $transaction->type === 'deposit' ? '#28a745' : '#dc3545' }};
        }

        .receipt-number {
            text-align: center;
            margin: 20px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .receipt-number-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .receipt-number-value {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            font-family: 'Courier New', 'Consolas', 'Monaco', monospace;
            letter-spacing: 0.5px;
        }

        .receipt-body {
            margin: 20px 0;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }

        .receipt-row:hover {
            background-color: #f8f9fa;
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        .receipt-label {
            font-weight: 600;
            width: 45%;
            color: #495057;
            font-size: 12px;
        }

        .receipt-value {
            width: 55%;
            text-align: right;
            color: #212529;
            font-size: 12px;
            font-weight: 500;
        }

        .amount-section {
            text-align: center;
            margin: 20px 0;
            padding: 15px 20px;
            border: 2px solid {{ $transaction->type === 'deposit' ? '#28a745' : '#dc3545' }};
            background: linear-gradient(135deg, {{ $transaction->type === 'deposit' ? 'rgba(40, 167, 69, 0.05)' : 'rgba(220, 53, 69, 0.05)' }} 0%, rgba(255, 255, 255, 1) 100%);
            border-radius: 6px;
            position: relative;
            overflow: hidden;
        }

        .amount-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, {{ $transaction->type === 'deposit' ? 'rgba(40, 167, 69, 0.1)' : 'rgba(220, 53, 69, 0.1)' }} 0%, transparent 70%);
            pointer-events: none;
        }

        .amount-section .amount-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .amount-section .amount-value {
            font-size: 20px;
            font-weight: 700;
            color: {{ $transaction->type === 'deposit' ? '#28a745' : '#dc3545' }};
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            gap: 50px;
        }

        .signature-box {
            flex: 1;
            min-height: 140px;
            position: relative;
        }

        .signature-box.left {
            text-align: left;
        }

        .signature-box.right {
            text-align: right;
        }

        .signature-label {
            font-weight: 600;
            font-size: 11px;
            color: #495057;
            margin-bottom: 55px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 0;
            min-height: 70px;
            display: block;
            width: 100%;
        }

        .receipt-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #dee2e6;
            text-align: center;
        }

        .receipt-footer p {
            margin: 5px 0;
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
        }

        .receipt-footer .thank-you {
            font-size: 12px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 8px;
        }

        .print-button {
            text-align: center;
            margin: 30px 0 0 0;
        }

        .btn-print {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>{{ getSetting('app_name', 'Money Point') }}</h1>
            <h2>Transaction Receipt</h2>
            <div class="transaction-badge">{{ ucwords($transaction->type) }}</div>
        </div>

        <div class="receipt-number">
            <div class="receipt-number-label">Receipt Number</div>
            <div class="receipt-number-value">#{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}</div>
        </div>

        <div class="receipt-body">
            <div class="receipt-row">
                <span class="receipt-label">Date & Time:</span>
                <span
                    class="receipt-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, h:i A') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Transaction ID:</span>
                <span class="receipt-value"
                    style="font-family: 'Courier New', 'Consolas', 'Monaco', monospace; font-size: 10px; letter-spacing: 0.3px;">{{ $transaction->uuid }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Teller Name:</span>
                <span class="receipt-value">{{ $transaction->user->name ?? 'N/A' }}</span>
            </div>
            @if ($transaction->reference)
                <div class="receipt-row">
                    <span class="receipt-label">Reference:</span>
                    <span class="receipt-value">{{ $transaction->reference }}</span>
                </div>
            @endif

            <div class="divider"></div>

            @if ($transaction->metadata && isset($transaction->metadata['customer_name']))
                <div class="receipt-row">
                    <span class="receipt-label">Customer Name:</span>
                    <span class="receipt-value">{{ $transaction->metadata['customer_name'] }}</span>
                </div>
            @endif
            @if ($transaction->metadata)
                @if (isset($transaction->metadata['customer_phone']))
                    @php
                        $phone = $transaction->metadata['customer_phone'];
                        $phoneLength = strlen($phone);
                        if ($phoneLength > 6) {
                            $maskedPhone =
                                substr($phone, 0, 4) . str_repeat('*', $phoneLength - 6) . substr($phone, -2);
                        } else {
                            $maskedPhone = str_repeat('*', $phoneLength);
                        }
                    @endphp
                    <div class="receipt-row">
                        <span class="receipt-label">Customer Phone:</span>
                        <span class="receipt-value">{{ $maskedPhone }}</span>
                    </div>
                @endif
                @if (isset($transaction->metadata['account_no']))
                    @php
                        $account = $transaction->metadata['account_no'];
                        $accountLength = strlen($account);
                        if ($accountLength > 6) {
                            $maskedAccount =
                                substr($account, 0, 4) . str_repeat('*', $accountLength - 6) . substr($account, -2);
                        } else {
                            $maskedAccount = str_repeat('*', $accountLength);
                        }
                    @endphp
                    <div class="receipt-row">
                        <span class="receipt-label">Account Number:</span>
                        <span class="receipt-value">{{ $maskedAccount }}</span>
                    </div>
                @endif
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-label">Transaction Amount</div>
            <div class="amount-value">{{ formatCurrency($amount, 2) }}</div>
        </div>

        <div class="signature-section">
            <div class="signature-box left">
                <div class="signature-label">Customer Signature</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box right">
                <div class="signature-label">Teller Signature</div>
                <div class="signature-line"></div>
            </div>
        </div>

        <div class="receipt-footer">
            <p class="thank-you">Thank You For Your Transaction!</p>
            <p>For inquiries, please contact our support team.</p>
            <p style="margin-top: 12px; font-size: 9px; color: #adb5bd;">
                Printed on {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="print-button no-print">
            <button class="btn-print" onclick="window.print()">
                🖨️ Print Receipt
            </button>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional - can be removed if not desired)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>

</html>
