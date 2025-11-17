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

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Account Ledger - {{ $account->user->name ?? 'System' }} ({{ ucwords($account->account_type) }} - {{ $account->provider ?? 'N/A' }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Current Balance:</strong> 
                                        @if($account->account_type === 'float')
                                            {{ formatCurrency(abs($account->balance), 0) }}
                                        @else
                                            {{ formatCurrency($account->balance, 0) }}
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>System Balance:</strong> {{ formatCurrency($account->balance, 0) }}
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table-borderless table-rounded mb-0 table" id="ledger-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Balance After</th>
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
                                                    <td>
                                                        @php
                                                            $amount = $line->amount;
                                                            $class = $amount >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
                                                            $sign = $amount >= 0 ? '+' : '';
                                                        @endphp
                                                        <span class="{{ $class }}">{{ $sign }}{{ formatCurrency($amount, 0) }}</span>
                                                    </td>
                                                    <td><strong>{{ formatCurrency($line->balance_after, 0) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#ledger-table')) {
                $('#ledger-table').DataTable().destroy();
            }

            var defaultPageLength = {{ getSetting('default_page_length', 10) }};
            $('#ledger-table').DataTable({
                pageLength: defaultPageLength,
                order: [[0, 'desc']] // Sort by date descending
            });
        });
    </script>
@endpush
@endsection

