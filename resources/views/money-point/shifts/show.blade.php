@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    @php
                        $dateFormat = getSetting('date_format', 'Y-m-d');
                        $timeFormat = getSetting('time_format', 'H:i:s');
                        $dateTimeFormat = "$dateFormat $timeFormat";
                        $statusText = ucwords(str_replace('_', ' ', $shift->status));
                        $bgClass = match ($shift->status) {
                            'open' => 'primary',
                            'submitted' => 'warning',
                            'verified' => 'success',
                            'closed' => 'secondary',
                            'discrepancy' => 'danger',
                            'pending_teller_acceptance' => 'info',
                            'pending_teller_confirmation' => 'warning',
                            'rejected' => 'danger',
                            default => 'secondary',
                        };
                    @endphp

                    <div class="col-12">
                        <div class="contact-list-wrap mb-25">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>
                                <div class="action-btn">
                                    <div class="drawer-btn d-flex justify-content-center gap-2">
                                        @if ($shift->canConfirm() && Auth()->user()->can('Submit Shifts'))
                                            <form action="{{ route('money-point.shifts.confirm-funds', $shift->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-default btn-squared"
                                                    onclick="return confirm('Confirm that the funds you received match the amounts shown in the system?')">
                                                    <i class="las la-check-circle me-1"></i>Confirm Funds
                                                </button>
                                            </form>
                                        @endif
                                        @if ($shift->canSubmit() && Auth::id() == $shift->teller_id && Auth()->user()->can('Submit Shifts'))
                                            <button type="button" class="btn btn-warning btn-sm btn-default btn-squared"
                                                data-bs-toggle="modal" data-bs-target="#submitShiftModal">
                                                <i class="las la-check-circle me-1"></i>Close Shift
                                            </button>
                                        @endif
                                        @if ($shift->canVerify() && Auth()->user()->can('Verify Shifts'))
                                            <button type="button" class="btn btn-success btn-sm btn-default btn-squared"
                                                data-bs-toggle="modal" data-bs-target="#verifyShiftModal">
                                                <i class="las la-check-double me-1"></i>Verify Shift
                                            </button>
                                        @endif
                                        @if ($shift->isPendingAcceptance() && Auth::id() == $shift->teller_id && Auth()->user()->can('Submit Shifts'))
                                            <form action="{{ route('money-point.shifts.accept', $shift->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-default btn-squared"
                                                    onclick="return confirm('Are you sure you want to accept this shift?')">
                                                    <i class="las la-check-circle me-1"></i>Accept Shift
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm btn-default btn-squared"
                                                data-bs-toggle="modal" data-bs-target="#rejectShiftModal">
                                                <i class="las la-times-circle me-1"></i>Reject Shift
                                            </button>
                                        @endif
                                        <a href="{{ route('money-point.shifts') }}"
                                            class="btn btn-primary btn-sm btn-default btn-squared">
                                            <i class="las la-arrow-left me-1"></i>Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">
                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <!-- Shift Information -->
                                <div class="card border-0 mb-25">
                                    <div class="card-header border-bottom">
                                        <h5 class="mb-0 fw-bold">Shift Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-muted mb-1">Teller</label>
                                                <div class="fw-bold">{{ $shift->teller->name ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-muted mb-1">Treasurer</label>
                                                <div class="fw-bold">{{ $shift->treasurer->name ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-muted mb-1">Status</label>
                                                <div>
                                                    <div class="userDatatable-content d-inline-block">
                                                        <span
                                                            class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $statusText }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-muted mb-1">Opened At</label>
                                                <div class="fw-bold">
                                                    {{ \Carbon\Carbon::parse($shift->opened_at)->format($dateTimeFormat) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($shift->isRejected() && $shift->rejection_reason)
                                    <!-- Rejection Information -->
                                    <div class="card border-0 mb-25 border-danger">
                                        <div class="card-header border-bottom bg-danger bg-opacity-10">
                                            <h5 class="mb-0 fw-bold text-danger">
                                                <i class="las la-exclamation-triangle me-2"></i>Shift Rejected
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-danger mb-0">
                                                <h6 class="alert-heading fw-bold mb-2">Rejection Reason:</h6>
                                                <p class="mb-2">{{ $shift->rejection_reason }}</p>
                                                @if ($shift->rejected_at)
                                                    <hr>
                                                    <small class="text-muted">
                                                        <i class="las la-clock me-1"></i>
                                                        Rejected on: {{ \Carbon\Carbon::parse($shift->rejected_at)->format($dateTimeFormat) }}
                                                    </small>
                                                @endif
                                            </div>
                                            @if (Auth()->user()->can('Verify Shifts'))
                                                <div class="mt-3">
                                                    <p class="text-muted mb-2">
                                                        <strong>Action Required:</strong> Please review the teller's concerns and make necessary corrections. 
                                                        After fixing the issues, you can reopen this shift for the teller to review again.
                                                    </p>
                                                    <form action="{{ route('money-point.shifts.reopen', $shift->id) }}" method="POST" class="mt-2">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label for="reopen_notes" class="form-label small">Notes (optional):</label>
                                                            <textarea class="form-control form-control-sm" id="reopen_notes" name="notes" rows="2" placeholder="Describe what corrections were made..."></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm btn-default btn-squared"
                                                            onclick="return confirm('Are you sure you want to reopen this shift? The teller will be able to review and accept/reject again.')">
                                                            <i class="las la-redo me-1"></i>Reopen Shift
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($shift->isPendingConfirmation() && Auth::id() == $shift->teller_id)
                                    <!-- Pending Confirmation Alert - Only show to teller -->
                                    <div class="alert alert-warning mb-25">
                                        <h6 class="alert-heading fw-bold mb-2">
                                            <i class="las la-exclamation-triangle me-2"></i>Funds Confirmation Required
                                        </h6>
                                        <p class="mb-3">
                                            <strong>Action Required:</strong> Please verify that the funds you received from the treasurer match the amounts shown below. 
                                            You cannot perform transactions until you confirm the funds.
                                        </p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Expected Opening Cash:</strong> {{ formatCurrency($shift->opening_cash ?? 0, 0) }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Expected Opening Floats:</strong>
                                                @if ($shift->opening_floats)
                                                    @foreach ($shift->opening_floats as $provider => $amount)
                                                        <div class="ms-2">
                                                            {{ $providerNames[$provider] ?? ucfirst($provider) }}: {{ formatCurrency(abs($amount), 0) }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($shift->isPendingAcceptance())
                                    <!-- Pending Acceptance Alert -->
                                    <div class="alert alert-info mb-25">
                                        <h6 class="alert-heading fw-bold mb-2">
                                            <i class="las la-info-circle me-2"></i>Shift Pending Your Acceptance
                                        </h6>
                                        <p class="mb-0">
                                            The treasurer has verified the shift amounts. Please review and either accept or reject this shift.
                                            If you notice any discrepancies, please reject and provide a reason for the treasurer to review.
                                        </p>
                                    </div>
                                @endif

                                <!-- Opening Balances -->
                                <div class="card border-0 mb-25">
                                    <div class="card-header border-bottom">
                                        <h5 class="mb-0 fw-bold">Opening Balances</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Opening Cash</label>
                                                <div class="fs-4 fw-bold text-primary">
                                                    {{ formatCurrency($actualStartingCash ?? $shift->opening_cash, 0) }}
                                                </div>
                                                @if (($actualStartingCash ?? $shift->opening_cash) != $shift->opening_cash)
                                                    <small class="text-success">
                                                        <i class="las la-info-circle"></i> Includes previous closing balance
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Mtaji (Opening Capital)</label>
                                                <div class="fs-4 fw-bold text-success">
                                                    {{ formatCurrency($mtaji, 0) }}
                                                </div>
                                                <small class="text-muted">Total: Cash + All Floats</small>
                                            </div>
                                        </div>

                                        @if (($actualStartingFloats ?? $shift->opening_floats) && count($actualStartingFloats ?? $shift->opening_floats) > 0)
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label text-muted mb-2">Opening Floats</label>
                                                </div>
                                                @foreach ($actualStartingFloats ?? $shift->opening_floats as $provider => $amount)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="border rounded p-3">
                                                            <label
                                                                class="form-label text-muted mb-1 small">{{ $providerNames[$provider] ?? ucfirst($provider) }}</label>
                                                            <div class="fw-bold">{{ formatCurrency(abs($amount), 0) }}
                                                            </div>
                                                            @if (isset($actualStartingFloats[$provider]) &&
                                                                    isset($shift->opening_floats[$provider]) &&
                                                                    abs($actualStartingFloats[$provider]) != abs($shift->opening_floats[$provider]))
                                                                <small class="text-success">
                                                                    <i class="las la-info-circle"></i> Includes previous
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Closing Balances (if submitted) -->
                                @if ($shift->closing_cash !== null)
                                    <div class="card border-0 mb-25">
                                        <div class="card-header border-bottom">
                                            <h5 class="mb-0 fw-bold">Closing Balances</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted mb-1">Closing Cash</label>
                                                    <div class="fs-5 fw-bold text-success">
                                                        {{ formatCurrency($shift->closing_cash, 0) }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted mb-1">Expected Cash</label>
                                                    <div class="fs-5 fw-bold text-info">
                                                        {{ formatCurrency($shift->expected_closing_cash ?? 0, 0) }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted mb-1">Variance</label>
                                                    <div
                                                        class="fs-5 fw-bold text-{{ ($shift->variance_cash ?? 0) == 0 ? 'success' : 'danger' }}">
                                                        {{ formatCurrency($shift->variance_cash ?? 0, 0) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted mb-1">Balanced (Closing
                                                        Capital)</label>
                                                    <div
                                                        class="fs-5 fw-bold text-{{ $balanced == $mtaji ? 'success' : 'warning' }}">
                                                        {{ formatCurrency($balanced, 0) }}
                                                    </div>
                                                    <small class="text-muted">
                                                        Mtaji: {{ formatCurrency($mtaji, 0) }}
                                                        @if ($balanced != $mtaji)
                                                            <span class="text-danger">(Not Balanced)</span>
                                                        @else
                                                            <span class="text-success">(Balanced)</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>

                                            @if ($shift->closing_floats && count($shift->closing_floats) > 0)
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <label class="form-label text-muted mb-2">Closing Floats</label>
                                                    </div>
                                                    @foreach ($shift->closing_floats ?? [] as $provider => $amount)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="border rounded p-3">
                                                                <label
                                                                    class="form-label text-muted mb-1 small">{{ $providerNames[$provider] ?? ucfirst($provider) }}</label>
                                                                <div class="fw-bold text-success">
                                                                    {{ formatCurrency($amount, 0) }}</div>
                                                                @if (isset($shift->expected_closing_floats[$provider]))
                                                                    @php
                                                                        // Expected closing float should be positive (display value)
                                                                        $expectedFloat =
                                                                            $shift->expected_closing_floats[$provider];
                                                                        // If it's negative, it means it was stored incorrectly, recalculate
if ($expectedFloat < 0) {
    $openingFloat =
        $shift->opening_floats[$provider] ?? 0;
    $floatAccount = \App\Models\Account::where(
        'user_id',
        $shift->teller_id,
    )
        ->where('account_type', 'float')
        ->where('provider', $provider)
        ->first();
    if ($floatAccount) {
        $floatChanges = \App\Models\TransactionLine::whereHas(
            'transaction',
            function ($q) use ($shift) {
                $q->where(
                    'teller_shift_id',
                    $shift->id,
                )->where(
                    'type',
                    '!=',
                    'allocation',
                );
            },
        )
            ->where(
                'account_id',
                $floatAccount->id,
            )
            ->sum('amount');
                                                                                $expectedFloat =
                                                                                    $openingFloat + $floatChanges;
                                                                            } else {
                                                                                $expectedFloat = abs($expectedFloat);
                                                                            }
                                                                        }
                                                                        // Variance: reported - expected (both in display format)
                                                                        $variance = $amount - $expectedFloat;
                                                                    @endphp
                                                                    <small class="text-muted d-block mt-1">
                                                                        Expected:
                                                                        <strong>{{ formatCurrency($expectedFloat, 0) }}</strong>
                                                                    </small>
                                                                    <small
                                                                        class="text-{{ $variance == 0 ? 'success' : 'danger' }} d-block mt-1">
                                                                        Variance:
                                                                        <strong>{{ formatCurrency(abs($variance), 0) }}</strong>
                                                                        @if ($variance != 0)
                                                                            ({{ $variance > 0 ? 'Over' : 'Short' }})
                                                                        @endif
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Notes (if exists) -->
                                @if ($shift->notes)
                                    <div class="card border-0 mb-25">
                                        <div class="card-header border-bottom">
                                            <h5 class="mb-0 fw-bold">Notes</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $shift->notes }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Transactions Table -->
                                <div class="card border-0 mb-25">
                                    <div
                                        class="card-header border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-bold">Transactions</h5>
                                        <div class="userDatatable-content d-inline-block">
                                            <span
                                                class="bg-opacity-primary color-primary userDatatable-content-status active">{{ $shift->transactions->count() }}
                                                transaction(s)</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($shift->transactions->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Reference</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($shift->transactions as $transaction)
                                                            @php
                                                                $typeText = ucwords($transaction->type);
                                                                $bgClass = match ($transaction->type) {
                                                                    'deposit' => 'success',
                                                                    'withdrawal' => 'danger',
                                                                    'allocation' => 'primary',
                                                                    'transfer' => 'info',
                                                                    'reconciliation' => 'warning',
                                                                    'adjustment' => 'secondary',
                                                                    default => 'secondary',
                                                                };
                                                            @endphp
                                                            @php
                                                                // For withdrawals and deposits, get the cash line amount (actual transaction amount)
                                                                $transactionAmount = 0;
                                                                if (
                                                                    in_array($transaction->type, [
                                                                        'withdrawal',
                                                                        'deposit',
                                                                    ])
                                                                ) {
                                                                    $cashLine = $transaction
                                                                        ->lines()
                                                                        ->whereHas('account', function ($q) {
                                                                            $q->where('account_type', 'cash');
                                                                        })
                                                                        ->first();

                                                                    if ($cashLine) {
                                                                        $transactionAmount = abs($cashLine->amount);
                                                                    } else {
                                                                        // Fallback: sum positive amounts
                                                                        $transactionAmount = abs(
                                                                            $transaction
                                                                                ->lines()
                                                                                ->where('amount', '>', 0)
                                                                                ->sum('amount'),
                                                                        );
                                                                    }
                                                                } else {
                                                                    // For other transaction types (allocation, transfer, etc.), sum positive amounts
                                                                    $transactionAmount = abs(
                                                                        $transaction
                                                                            ->lines()
                                                                            ->where('amount', '>', 0)
                                                                            ->sum('amount'),
                                                                    );
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td><strong>#{{ $transaction->id }}</strong></td>
                                                                <td>
                                                                    <div class="userDatatable-content d-inline-block">
                                                                        <span
                                                                            class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="fw-bold">
                                                                    {{ formatCurrency($transactionAmount, 0) }}</td>
                                                                <td>{{ $transaction->reference ?? 'N/A' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format($dateTimeFormat) }}
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-primary btn-xs btn-default btn-squared view-transaction"
                                                                        data-id="{{ $transaction->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#transactionDetailsModal">
                                                                        <i class="las la-eye me-1"></i>View
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="las la-inbox text-muted fs-1"></i>
                                                <p class="text-muted mt-2 mb-0">No transactions found for this shift.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($shift->canSubmit() && Auth::id() == $shift->teller_id && Auth()->user()->can('Submit Shifts'))
        @include('money-point.shifts.submit-modal')
    @endif

    @if ($shift->canVerify() && Auth()->user()->can('Verify Shifts'))
        @include('money-point.shifts.verify-modal')
    @endif

    @if ($shift->isPendingAcceptance() && Auth::id() == $shift->teller_id && Auth()->user()->can('Submit Shifts'))
        @include('money-point.shifts.reject-modal')
    @endif

    @can('View Money Point Transactions')
        @include('money-point.transactions.show-modal')
    @endcan

    @push('page_scripts')
        <script>
            $(document).ready(function() {
                // Currency formatting function
                function formatCurrency(amount, decimals = 0) {
                    if (amount === null || amount === undefined || isNaN(amount)) {
                        return 'TZS 0';
                    }
                    var formatted = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    }).format(amount);
                    return 'TZS ' + formatted;
                }

                // Real-time Balanced calculation for Close Shift modal
                var mtaji = {{ $mtaji }};

                function calculateBalanced() {
                    var closingCash = 0;
                    var closingFloats = 0;

                    // Get closing cash value
                    var cashInput = $('#closing_cash');
                    if (cashInput.length && cashInput.val()) {
                        var cashValue = cashInput.val().toString().replace(/[^\d]/g, '');
                        closingCash = parseInt(cashValue) || 0;
                    }

                    // Get closing floats values
                    $('input[id^="closing_floats_"]').each(function() {
                        if ($(this).val()) {
                            var floatValue = $(this).val().toString().replace(/[^\d]/g, '');
                            closingFloats += parseInt(floatValue) || 0;
                        }
                    });

                    var balanced = closingCash + closingFloats;

                    // Update display
                    $('#calculatedBalanced').text(formatCurrency(balanced, 0));

                    // Update status
                    var statusDiv = $('#balancedStatus');
                    var statusText = $('#balancedStatusText');

                    if (balanced === 0) {
                        statusDiv.removeClass('alert-success alert-warning alert-danger').addClass('alert-secondary');
                        statusText.html(
                            '<i class="las la-info-circle me-2"></i>Enter closing amounts to see if balanced');
                    } else if (balanced === mtaji) {
                        statusDiv.removeClass('alert-secondary alert-warning alert-danger').addClass('alert-success');
                        statusText.html(
                            '<i class="las la-check-circle me-2"></i><strong>Balanced!</strong> Closing capital matches Mtaji (Opening Capital)'
                        );
                    } else {
                        var difference = balanced - mtaji;
                        var diffText = Math.abs(difference).toLocaleString('en-US');
                        if (difference > 0) {
                            statusDiv.removeClass('alert-secondary alert-success alert-danger').addClass(
                                'alert-warning');
                            statusText.html(
                                '<i class="las la-exclamation-triangle me-2"></i><strong>Not Balanced:</strong> Over by ' +
                                formatCurrency(difference, 0));
                        } else {
                            statusDiv.removeClass('alert-secondary alert-success alert-warning').addClass(
                                'alert-danger');
                            statusText.html(
                                '<i class="las la-times-circle me-2"></i><strong>Not Balanced:</strong> Short by ' +
                                formatCurrency(Math.abs(difference), 0));
                        }
                    }
                }

                // Calculate on input change
                $(document).on('input', '#closing_cash, input[id^="closing_floats_"]', function() {
                    calculateBalanced();
                });

                // Initialize calculation when modal is shown
                $('#submitShiftModal').on('shown.bs.modal', function() {
                    calculateBalanced();
                });

                // Verify Shift Modal - Adjustments handling
                $('#verify_action').on('change', function() {
                    if ($(this).val() === 'request_adjustment') {
                        $('#adjustments-section').show();
                    } else {
                        $('#adjustments-section').hide();
                        $('#adjustments-container').empty();
                    }
                });

                $('#add-adjustment').on('click', function() {
                    var adjustmentHtml = `
                        <div class="row mb-2 adjustment-row border-bottom pb-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Account ID</label>
                                <input type="number" class="form-control form-control-sm" name="adjustments[][account_id]" placeholder="Account ID" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Amount</label>
                                <input type="text" class="form-control form-control-sm amount" name="adjustments[][amount]" placeholder="Amount" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">Reason</label>
                                <input type="text" class="form-control form-control-sm" name="adjustments[][reason]" placeholder="Reason" required>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small text-muted mb-1">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-danger w-100 remove-adjustment">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    $('#adjustments-container').append(adjustmentHtml);

                    // Initialize inputmask for amount
                    $('.amount').inputmask({
                        alias: 'numeric',
                        groupSeparator: ',',
                        autoGroup: true,
                        digits: 2,
                        rightAlign: true,
                        prefix: 'TZS ',
                        placeholder: '0'
                    });
                });

                $(document).on('click', '.remove-adjustment', function() {
                    $(this).closest('.adjustment-row').remove();
                });

                // Load transaction details when modal is opened
                $('#transactionDetailsModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var transactionId = button.data('id');
                    var modal = $(this);

                    // Reset content to loading state
                    modal.find('#transactionDetailsContent').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading transaction details...</p>
                        </div>
                    `);

                    // Load transaction details via AJAX
                    $.ajax({
                        url: "{{ route('money-point.transactions.show', ':id') }}".replace(':id',
                            transactionId),
                        method: 'GET',
                        success: function(response) {
                            modal.find('#transactionDetailsContent').html(response.html);
                        },
                        error: function(xhr) {
                            modal.find('#transactionDetailsContent').html(`
                                <div class="alert alert-danger border-0">
                                    <i class="las la-exclamation-triangle me-2"></i>
                                    <strong>Error:</strong> Failed to load transaction details. Please try again.
                                </div>
                            `);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
