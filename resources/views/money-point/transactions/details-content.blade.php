@php
    $dateFormat = getSetting('date_format', 'Y-m-d');
    $timeFormat = getSetting('time_format', 'H:i:s');
    $dateTimeFormat = "$dateFormat $timeFormat";
@endphp

<!-- Transaction Information -->
<div class="card border-0 shadow-sm mb-4" data-transaction-type="{{ $transaction->type }}"
    data-transaction-id="{{ $transaction->id }}">
    <div class="card-body p-3">
        <h6 class="card-title fw-bold mb-3 text-primary">
            <i class="las la-info-circle me-2"></i>Transaction Information
        </h6>
        <div class="row">
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Type</small>
                <div class="userDatatable-content d-inline-block">
                    <span
                        class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">User</small>
                <strong class="text-dark">{{ $transaction->user->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Reference</small>
                <strong class="text-dark">{{ $transaction->reference ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3 mb-3">
                <small class="text-muted d-block mb-1">Created At</small>
                <strong
                    class="text-dark">{{ \Carbon\Carbon::parse($transaction->created_at)->format($dateTimeFormat) }}</strong>
            </div>
        </div>
        @if ($transaction->tellerShift)
            <div class="row mt-2">
                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Shift</small>
                    <strong class="text-dark">
                        {{ $transaction->tellerShift->teller->name ?? 'N/A' }} -
                        {{ \Carbon\Carbon::parse($transaction->tellerShift->opened_at)->format($dateFormat) }}
                    </strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Shift Status</small>
                    @php
                        $statusClass = match ($transaction->tellerShift->status) {
                            'open' => 'primary',
                            'submitted' => 'warning',
                            'verified' => 'success',
                            'closed' => 'secondary',
                            'discrepancy' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <div class="userDatatable-content d-inline-block">
                        <span
                            class="bg-opacity-{{ $statusClass }} color-{{ $statusClass }} userDatatable-content-status active">
                            {{ ucwords($transaction->tellerShift->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        @if (
            $transaction->metadata &&
                (isset($transaction->metadata['customer_name']) ||
                    isset($transaction->metadata['customer_phone']) ||
                    isset($transaction->metadata['account_no'])))
            <div class="row mt-3">
                <div class="col-12">
                    <small class="text-muted d-block mb-2">Customer Information</small>
                    <div class="border-top pt-2">
                        @if (isset($transaction->metadata['customer_name']))
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Customer Name</small>
                                <strong class="text-dark">{{ $transaction->metadata['customer_name'] }}</strong>
                            </div>
                        @endif
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
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Customer Phone</small>
                                <strong class="text-dark">{{ $maskedPhone }}</strong>
                            </div>
                        @endif
                        @if (isset($transaction->metadata['account_no']))
                            @php
                                $account = $transaction->metadata['account_no'];
                                $accountLength = strlen($account);
                                if ($accountLength > 6) {
                                    $maskedAccount =
                                        substr($account, 0, 4) .
                                        str_repeat('*', $accountLength - 6) .
                                        substr($account, -2);
                                } else {
                                    $maskedAccount = str_repeat('*', $accountLength);
                                }
                            @endphp
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Account Number</small>
                                <strong class="text-dark">{{ $maskedAccount }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Transaction Lines -->
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Transaction Lines</h6>
        <div class="userDatatable-content d-inline-block">
            <span
                class="bg-opacity-primary color-primary userDatatable-content-status active">{{ $transaction->lines->count() }}
                line(s)</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Account Owner</th>
                        <th>Account Type</th>
                        <th>Provider</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->lines as $line)
                        <tr>
                            <td class="ps-3">
                                <strong>{{ $line->account->user->name ?? 'System' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucwords($line->account->account_type) }}</span>
                            </td>
                            <td>{{ $line->account->provider ?? 'N/A' }}</td>
                            <td class="text-end">
                                <span class="fw-bold {{ $line->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $line->amount >= 0 ? '+' : '' }}{{ formatCurrency($line->amount, 0) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong>{{ formatCurrency($line->balance_after, 0) }}</strong>
                            </td>
                            <td>{{ $line->description ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No transaction lines found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
