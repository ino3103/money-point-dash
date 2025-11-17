@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    @include('alerts.success')
                    @include('alerts.errors')
                    @include('alerts.error')

                    @php
                        $dateFormat = getSetting('date_format', 'Y-m-d');
                        $timeFormat = getSetting('time_format', 'H:i:s');
                        $dateTimeFormat = "$dateFormat $timeFormat";
                    @endphp

                    @if ($openShift)
                        <div class="col-xxl-12 mb-25">
                            <div class="alert alert-info border-0" role="alert">
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div>
                                        <h5 class="alert-heading mb-1">Active Shift</h5>
                                        <p class="mb-0">You have an open shift. Opening Cash:
                                            {{ formatCurrency($openShift->opening_cash, 0) }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('money-point.shifts.show', $openShift->id) }}"
                                            class="btn btn-primary btn-sm">View Shift</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alerts Section -->
                    @if ($stats['pending_verification'] > 0 || $stats['discrepancy_shifts'] > 0 || $stats['low_float_alerts']->count() > 0)
                        <div class="col-xxl-12 mb-25">
                            @if ($stats['pending_verification'] > 0)
                                <div class="alert alert-warning border-0 mb-2" role="alert">
                                    <i class="las la-exclamation-triangle me-2"></i>
                                    <strong>Pending Verification:</strong> {{ $stats['pending_verification'] }} shift(s)
                                    waiting for verification.
                                    <a href="{{ route('money-point.shifts') }}?status=submitted" class="alert-link">View
                                        Shifts</a>
                                </div>
                            @endif
                            @if ($stats['discrepancy_shifts'] > 0)
                                <div class="alert alert-danger border-0 mb-2" role="alert">
                                    <i class="las la-exclamation-circle me-2"></i>
                                    <strong>Discrepancies Found:</strong> {{ $stats['discrepancy_shifts'] }} shift(s) with
                                    discrepancies.
                                    <a href="{{ route('money-point.shifts') }}?status=discrepancy" class="alert-link">View
                                        Shifts</a>
                                </div>
                            @endif
                            @if ($stats['low_float_alerts']->count() > 0)
                                <div class="alert alert-warning border-0 mb-2" role="alert">
                                    <i class="las la-wallet me-2"></i>
                                    <strong>Low Float Alert:</strong>
                                    @foreach ($stats['low_float_alerts'] as $alert)
                                        {{ ucfirst($alert['provider']) }} ({{ $alert['user'] }}):
                                        {{ formatCurrency($alert['balance'], 0) }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-exchange-alt fs-1 text-primary"></i>
                                </div>
                                <h5 class="mb-1">Today's Transactions</h5>
                                <h2 class="text-primary mb-0">{{ $stats['today_transactions_count'] }}</h2>
                                <small class="text-muted">
                                    @if ($stats['today_deposits_count'] > 0)
                                        {{ $stats['today_deposits_count'] }}
                                        deposit{{ $stats['today_deposits_count'] > 1 ? 's' : '' }}
                                    @endif
                                    @if ($stats['today_deposits_count'] > 0 && $stats['today_withdrawals_count'] > 0)
                                        ,
                                    @endif
                                    @if ($stats['today_withdrawals_count'] > 0)
                                        {{ $stats['today_withdrawals_count'] }}
                                        withdrawal{{ $stats['today_withdrawals_count'] > 1 ? 's' : '' }}
                                    @endif
                                    @php
                                        $otherCount =
                                            $stats['today_transactions_count'] -
                                            $stats['today_deposits_count'] -
                                            $stats['today_withdrawals_count'];
                                    @endphp
                                    @if ($otherCount > 0)
                                        @if ($stats['today_deposits_count'] > 0 || $stats['today_withdrawals_count'] > 0)
                                            ,
                                        @endif
                                        {{ $otherCount }} other
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-cash-register fs-1 text-info"></i>
                                </div>
                                <h5 class="mb-1">Active Shifts</h5>
                                <h2 class="text-info mb-0">{{ $stats['active_shifts'] }}</h2>
                                <small class="text-muted">Currently open</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-check-circle fs-1 text-warning"></i>
                                </div>
                                <h5 class="mb-1">Pending Verification</h5>
                                <h2 class="text-warning mb-0">{{ $stats['pending_verification'] }}</h2>
                                <small class="text-muted">Awaiting approval</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-money-bill-wave fs-1 text-success"></i>
                                </div>
                                <h5 class="mb-1">Total Cash</h5>
                                <h2 class="text-success mb-0">{{ formatCurrency($stats['total_cash'], 0) }}</h2>
                                <small class="text-muted">In system</small>
                            </div>
                        </div>
                    </div>

                    <!-- Total Float Capital -->
                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-mobile-alt fs-1 text-info"></i>
                                </div>
                                <h5 class="mb-1">Total Float Capital</h5>
                                <h2 class="text-info mb-0">{{ formatCurrency($stats['total_float_capital'], 0) }}</h2>
                                <small class="text-muted">All providers</small>
                            </div>
                        </div>
                    </div>

                    <!-- Total Mtaji in System -->
                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-wallet fs-1 text-primary"></i>
                                </div>
                                <h5 class="mb-1">Total Mtaji</h5>
                                <h2 class="text-primary mb-0">{{ formatCurrency($stats['total_mtaji_in_system'], 0) }}</h2>
                                <small class="text-muted">Active shifts</small>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Completion Rate -->
                    <div class="col-xxl-3 col-md-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="las la-chart-line fs-1 text-success"></i>
                                </div>
                                <h5 class="mb-1">Shift Completion</h5>
                                <h2 class="text-success mb-0">{{ number_format($stats['shift_completion_rate'], 1) }}%</h2>
                                <small
                                    class="text-muted">{{ $stats['today_verified_shifts'] }}/{{ $stats['today_shifts_count'] }}
                                    today</small>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Summary -->
                    <div class="col-xxl-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-header pb-md-0 border-0 pb-20">
                                <h4 class="mb-0">Today's Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="las la-arrow-up fs-2 text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 text-muted">Total Deposits</h6>
                                                <h4 class="mb-0 text-success">
                                                    {{ formatCurrency($stats['today_deposit_amount'], 0) }}</h4>
                                                <small class="text-muted">{{ $stats['today_deposits_count'] }}
                                                    transaction(s)</small>
                                                @if ($stats['avg_deposit_size'] > 0)
                                                    <small class="text-muted d-block">Avg:
                                                        {{ formatCurrency($stats['avg_deposit_size'], 0) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="las la-arrow-down fs-2 text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 text-muted">Total Withdrawals</h6>
                                                <h4 class="mb-0 text-danger">
                                                    {{ formatCurrency($stats['today_withdrawal_amount'], 0) }}</h4>
                                                <small class="text-muted">{{ $stats['today_withdrawals_count'] }}
                                                    transaction(s)</small>
                                                @if ($stats['avg_withdrawal_size'] > 0)
                                                    <small class="text-muted d-block">Avg:
                                                        {{ formatCurrency($stats['avg_withdrawal_size'], 0) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Net Flow:</strong>
                                            <span
                                                class="fs-4 {{ $stats['today_deposit_amount'] - $stats['today_withdrawal_amount'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ formatCurrency($stats['today_deposit_amount'] - $stats['today_withdrawal_amount'], 0) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Avg Transactions/Shift:</strong>
                                            <span
                                                class="fw-bold">{{ number_format($stats['avg_transactions_per_shift'], 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Week Comparison -->
                    <div class="col-xxl-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-header pb-md-0 border-0 pb-20">
                                <h4 class="mb-0">This Week vs Last Week</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="las la-arrow-up fs-2 text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 text-muted">This Week Deposits</h6>
                                                <h4 class="mb-0 text-success">
                                                    {{ formatCurrency($stats['this_week_deposit_amount'], 0) }}</h4>
                                                @if ($stats['deposit_change_percent'] != 0)
                                                    <small
                                                        class="text-{{ $stats['deposit_change_percent'] > 0 ? 'success' : 'danger' }}">
                                                        <i
                                                            class="las la-{{ $stats['deposit_change_percent'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                                        {{ number_format(abs($stats['deposit_change_percent']), 1) }}% vs
                                                        last week
                                                    </small>
                                                @else
                                                    <small class="text-muted">No change</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="las la-arrow-down fs-2 text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 text-muted">This Week Withdrawals</h6>
                                                <h4 class="mb-0 text-danger">
                                                    {{ formatCurrency($stats['this_week_withdrawal_amount'], 0) }}</h4>
                                                @if ($stats['withdrawal_change_percent'] != 0)
                                                    <small
                                                        class="text-{{ $stats['withdrawal_change_percent'] > 0 ? 'success' : 'danger' }}">
                                                        <i
                                                            class="las la-{{ $stats['withdrawal_change_percent'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                                        {{ number_format(abs($stats['withdrawal_change_percent']), 1) }}%
                                                        vs last week
                                                    </small>
                                                @else
                                                    <small class="text-muted">No change</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            Last Week: Deposits {{ formatCurrency($stats['last_week_deposit_amount'], 0) }}
                                            |
                                            Withdrawals {{ formatCurrency($stats['last_week_withdrawal_amount'], 0) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Float Balances by Provider -->
                    <div class="col-xxl-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-header pb-md-0 border-0 pb-20">
                                <h4 class="mb-0">Float Balances by Provider</h4>
                            </div>
                            <div class="card-body">
                                @if ($stats['float_balances']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Provider</th>
                                                    <th>Teller</th>
                                                    <th class="text-end">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stats['float_balances'] as $provider => $data)
                                                    @foreach ($data['accounts'] as $account)
                                                        <tr>
                                                            <td>
                                                                @if ($loop->first)
                                                                    <strong>{{ $account['display_name'] }}</strong>
                                                                @endif
                                                            </td>
                                                            <td>{{ $account['user_name'] }}</td>
                                                            <td class="text-end">
                                                                <span
                                                                    class="text-{{ $account['system_balance'] <= -10000000 ? 'success' : ($account['system_balance'] <= -5000000 ? 'warning' : 'danger') }}">
                                                                    {{ formatCurrency($account['balance'], 0) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @if ($data['accounts_count'] > 1)
                                                        <tr class="table-info">
                                                            <td colspan="2" class="text-end"><strong>Subtotal
                                                                    ({{ $data['accounts_count'] }} accounts):</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ formatCurrency($data['total'], 0) }}</strong>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No float accounts found.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Cash vs Float Ratio & Performance Metrics -->
                    <div class="col-xxl-6 mb-25">
                        <div class="card border-0 h-100">
                            <div class="card-header pb-md-0 border-0 pb-20">
                                <h4 class="mb-0">Capital Distribution & Performance</h4>
                            </div>
                            <div class="card-body">
                                <!-- Cash vs Float Ratio -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Cash Ratio</span>
                                        <strong
                                            class="text-success">{{ number_format($stats['cash_ratio'], 1) }}%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $stats['cash_ratio'] }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Float Ratio</span>
                                        <strong class="text-info">{{ number_format($stats['float_ratio'], 1) }}%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ $stats['float_ratio'] }}%">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- Performance Metrics -->
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Discrepancy Rate</small>
                                        <strong
                                            class="text-{{ $stats['discrepancy_rate'] < 5 ? 'success' : ($stats['discrepancy_rate'] < 10 ? 'warning' : 'danger') }}">
                                            {{ number_format($stats['discrepancy_rate'], 1) }}%
                                        </strong>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Shifts Today</small>
                                        <strong>{{ $stats['today_shifts_count'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Providers by Volume -->
                    @if (count($stats['provider_volumes']) > 0)
                        <div class="col-xxl-6 mb-25">
                            <div class="card border-0 h-100">
                                <div class="card-header pb-md-0 border-0 pb-20">
                                    <h4 class="mb-0">Top Providers by Volume (Today)</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Provider</th>
                                                    <th class="text-end">Transactions</th>
                                                    <th class="text-end">Volume</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $topProviders = array_slice($stats['provider_volumes'], 0, 5, true);
                                                @endphp
                                                @foreach ($topProviders as $provider => $data)
                                                    <tr>
                                                        <td><strong>{{ $data['display_name'] }}</strong></td>
                                                        <td class="text-end">
                                                            <span class="badge bg-primary">{{ $data['count'] }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <strong>{{ formatCurrency($data['amount'], 0) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Transactions -->
                    @if ($stats['recent_transactions']->count() > 0)
                        <div class="col-xxl-6 mb-25">
                            <div class="card border-0 h-100">
                                <div
                                    class="card-header pb-md-0 border-0 pb-20 d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Recent Transactions</h4>
                                    <a href="{{ route('money-point.transactions') }}" class="btn btn-sm btn-primary">View
                                        All</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>User</th>
                                                    <th class="text-end">Amount</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stats['recent_transactions']->take(5) as $transaction)
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
                                                    <tr>
                                                        <td>
                                                            <div class="userDatatable-content d-inline-block">
                                                                <span
                                                                    class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                        <td class="text-end">
                                                            {{ formatCurrency($transaction->cached_amount ?? 0, 0) }}
                                                        </td>
                                                        <td>
                                                            <small>{{ \Carbon\Carbon::parse($transaction->created_at)->format($dateTimeFormat) }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Recent Shifts -->
                    @if ($recentShifts->count() > 0)
                        <div class="col-xxl-12 mb-25">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Shifts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Teller</th>
                                                    <th>Treasurer</th>
                                                    <th>Opening Cash</th>
                                                    <th>Status</th>
                                                    <th>Opened At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recentShifts as $shift)
                                                    <tr>
                                                        <td>{{ $shift->teller->name ?? 'N/A' }}</td>
                                                        <td>{{ $shift->treasurer->name ?? 'N/A' }}</td>
                                                        <td>{{ formatCurrency($shift->opening_cash, 0) }}</td>
                                                        <td>
                                                            @php
                                                                $statusText = ucwords($shift->status ?? 'Unknown');
                                                                $bgClass = match ($shift->status ?? '') {
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
                                                                    class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $statusText }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($shift->opened_at)->format($dateTimeFormat) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('money-point.shifts.show', $shift->id) }}"
                                                                class="btn btn-sm btn-primary">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
