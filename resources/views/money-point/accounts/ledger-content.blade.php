@php
    $dateFormat = getSetting('date_format', 'Y-m-d');
    $timeFormat = getSetting('time_format', 'H:i:s');
    $dateTimeFormat = "$dateFormat $timeFormat";

    // Account type badge
    $typeText = ucwords($account->account_type);
    $typeBgClass = match ($account->account_type) {
        'cash' => 'success',
        'float' => 'primary',
        'bank' => 'info',
        default => 'secondary',
    };

    // Provider badge
    $providerDisplay = '-';
    if ($account->provider && $account->provider !== 'cash') {
        $providerModel = \App\Models\FloatProvider::where('name', $account->provider)->first();
        $providerDisplay = $providerModel ? $providerModel->display_name : ucfirst($account->provider);
    }
@endphp

<!-- Account Information -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <h6 class="card-title fw-bold mb-3 text-primary">
            <i class="las la-wallet me-2"></i>Account Information
        </h6>
        <div class="row">
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Account Owner</small>
                <strong class="text-dark">{{ $account->user->name ?? 'System' }}</strong>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Account Type</small>
                <div class="userDatatable-content d-inline-block">
                    <span
                        class="bg-opacity-{{ $typeBgClass }} color-{{ $typeBgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Provider</small>
                @if ($providerDisplay !== '-')
                    <div class="userDatatable-content d-inline-block">
                        <span
                            class="bg-opacity-primary color-primary userDatatable-content-status active">{{ $providerDisplay }}</span>
                    </div>
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Current Balance</small>
                <strong class="text-dark fs-5">
                    @if ($account->account_type === 'float')
                        {{ formatCurrency(abs($account->balance), 0) }}
                    @else
                        {{ formatCurrency($account->balance, 0) }}
                    @endif
                </strong>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <small class="text-muted d-block mb-1">System Balance</small>
                <strong class="text-dark">{{ formatCurrency($account->balance, 0) }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom">
        <h6 class="mb-0 fw-bold">Transaction History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive p-2">
            <table class="table-borderless table-rounded mb-0 table w-100" id="ledger-table-modal">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Transaction Type</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                        <tr>
                            <td>
                                @php
                                    $dateFormat = getSetting('date_format', 'Y-m-d');
                                    $timeFormat = getSetting('time_format', 'H:i:s');
                                @endphp
                                {{ \Carbon\Carbon::parse($line->created_at)->format("$dateFormat $timeFormat") }}
                            </td>
                            <td>
                                @php
                                    $type = $line->transaction->type ?? 'N/A';
                                    $typeText = ucwords($type);
                                    $bgClass = match ($type) {
                                        'deposit' => 'success',
                                        'withdrawal' => 'danger',
                                        'allocation' => 'primary',
                                        'transfer' => 'info',
                                        'reconciliation' => 'warning',
                                        'adjustment' => 'secondary',
                                        'fee' => 'dark',
                                        default => 'secondary'
                                    };
                                @endphp
                                <div class="userDatatable-content d-inline-block">
                                    <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                                </div>
                            </td>
                            <td>{{ $line->description ?? 'N/A' }}</td>
                            <td class="text-end">
                                @php
                                    $amount = $line->amount;
                                    $class = $amount >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
                                    $sign = $amount >= 0 ? '+' : '';
                                @endphp
                                <span class="{{ $class }}">{{ $sign }}{{ formatCurrency($amount, 0) }}</span>
                            </td>
                            <td class="text-end"><strong>{{ formatCurrency($line->balance_after, 0) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
