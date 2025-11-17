@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="contact-list-wrap mb-25">
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>
                                <div class="action-btn d-flex gap-2">
                                    @can('Create Withdrawals')
                                        <button type="button" class="btn btn-danger btn-sm btn-default btn-squared"
                                            data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                            <i class="las la-arrow-down me-1"></i>Withdrawal
                                        </button>
                                    @endcan
                                    @can('Create Deposits')
                                        <button type="button" class="btn btn-success btn-sm btn-default btn-squared"
                                            data-bs-toggle="modal" data-bs-target="#depositModal">
                                            <i class="las la-arrow-up me-1"></i>Deposit
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">
                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <div class="table-responsive table-responsive--dynamic">
                                    <div class="row mb-3">
                                        <!-- Transaction Type Filter -->
                                        <div class="col-md-3">
                                            <label for="type-filter">Transaction Type:</label>
                                            <select id="type-filter" class="form-control select2">
                                                <option value="">All</option>
                                                <option value="deposit">Deposit</option>
                                                <option value="withdrawal">Withdrawal</option>
                                                <option value="allocation">Allocation</option>
                                                <option value="transfer">Transfer</option>
                                                <option value="reconciliation">Reconciliation</option>
                                                <option value="adjustment">Adjustment</option>
                                                <option value="fee">Fee</option>
                                            </select>
                                        </div>

                                        <!-- Shift Filter -->
                                        <div class="col-md-3">
                                            <label for="shift-filter">Shift:</label>
                                            <select id="shift-filter" class="form-control select2">
                                                <option value="">All Shifts</option>
                                                @foreach ($shifts ?? [] as $shiftOption)
                                                    <option value="{{ $shiftOption->id }}">
                                                        {{ $shiftOption->teller->name ?? 'N/A' }} -
                                                        {{ \Carbon\Carbon::parse($shiftOption->opened_at)->format('Y-m-d') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- User Filter -->
                                        <div class="col-md-3">
                                            <label for="user-filter">User:</label>
                                            <select id="user-filter" class="form-control select2">
                                                <option value="">All Users</option>
                                                @foreach ($users ?? [] as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Date Range Filter -->
                                        <div class="col-md-3">
                                            <label for="date-range-filter">Date Range:</label>
                                            <div id="reportrange"
                                                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table-borderless table-rounded mb-0 table" id="transactions-table">
                                        <thead>
                                            <tr>
                                                <th>S/n</th>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Reference</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $index => $transaction)
                                                <tr data-type="{{ $transaction->type }}" 
                                                    data-user-id="{{ $transaction->user_id }}" 
                                                    data-shift-id="{{ $transaction->teller_shift_id ?? '' }}" 
                                                    data-created-at="{{ $transaction->created_at->format('Y-m-d') }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @php
                                                            $typeText = ucwords($transaction->type);
                                                            $bgClass = match ($transaction->type) {
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
                                                    <td>{{ $transaction->user ? $transaction->user->name : 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            $amount = 0;
                                                            if (in_array($transaction->type, ['withdrawal', 'deposit'])) {
                                                                $cashLine = $transaction->lines->firstWhere('account.account_type', 'cash');
                                                                if ($cashLine) {
                                                                    $amount = abs($cashLine->amount);
                                                                }
                                                            } else {
                                                                $amount = abs($transaction->lines->where('amount', '>', 0)->sum('amount'));
                                                            }
                                                        @endphp
                                                        {{ formatCurrency($amount, 0) }}
                                                    </td>
                                                    <td>{{ $transaction->reference ?? 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            $dateFormat = getSetting('date_format', 'Y-m-d');
                                                            $timeFormat = getSetting('time_format', 'H:i:s');
                                                        @endphp
                                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format("$dateFormat $timeFormat") }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-xs view-transaction" data-id="{{ $transaction->id }}" data-bs-toggle="modal" data-bs-target="#transactionDetailsModal">View</button>
                                                    </td>
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
                if ($.fn.DataTable.isDataTable('#transactions-table')) {
                    $('#transactions-table').DataTable().destroy();
                }

                var defaultPageLength = {{ getSetting('default_page_length', 10) }};
                
                // Date Range Picker variables (declared before filter function)
                var start = moment().startOf('month');
                var end = moment().endOf('month');

                var table = $('#transactions-table').DataTable({
                    pageLength: defaultPageLength,
                    autoWidth: false,
                    responsive: true,
                    order: [[5, 'desc']] // Order by created_at descending
                });

                // Custom filter function
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var row = table.row(dataIndex).node();
                        
                        // Get filter values
                        var typeFilter = $('#type-filter').val();
                        var shiftFilter = $('#shift-filter').val();
                        var userFilter = $('#user-filter').val();
                        var dateStart = start ? start.format('YYYY-MM-DD') : null;
                        var dateEnd = end ? end.format('YYYY-MM-DD') : null;
                        
                        // Type filter
                        if (typeFilter && $(row).data('type') !== typeFilter) {
                            return false;
                        }
                        
                        // Shift filter
                        if (shiftFilter) {
                            var rowShiftId = $(row).data('shift-id') || '';
                            if (String(rowShiftId) !== String(shiftFilter)) {
                                return false;
                            }
                        }
                        
                        // User filter
                        if (userFilter) {
                            var rowUserId = $(row).data('user-id');
                            if (String(rowUserId) !== String(userFilter)) {
                                return false;
                            }
                        }
                        
                        // Date range filter
                        if (dateStart && dateEnd) {
                            var rowDate = $(row).data('created-at');
                            if (rowDate && (rowDate < dateStart || rowDate > dateEnd)) {
                                return false;
                            }
                        }
                        
                        return true;
                    }
                );

                // Filter change handlers - filter client-side
                $('#type-filter, #shift-filter, #user-filter').on('change', function() {
                    table.draw();
                });

                function cb(startDate, endDate) {
                    start = startDate;
                    end = endDate;
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    table.draw();
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')]
                    }
                }, cb);

                cb(start, end);

                // Initialize Select2 for filters
                $('#type-filter, #shift-filter, #user-filter').select2({
                    placeholder: 'Select...',
                    allowClear: true
                });

                // Initialize Select2 when modals are opened
                $('#withdrawModal').on('shown.bs.modal', function() {
                    if ($('#withdraw_provider').hasClass('select2-hidden-accessible')) {
                        $('#withdraw_provider').select2('destroy');
                    }
                    $('#withdraw_provider').select2({
                        dropdownParent: $('#withdrawModal'),
                        placeholder: 'Select Provider',
                        allowClear: false
                    });
                });

                $('#depositModal').on('shown.bs.modal', function() {
                    if ($('#deposit_provider').hasClass('select2-hidden-accessible')) {
                        $('#deposit_provider').select2('destroy');
                    }
                    $('#deposit_provider').select2({
                        dropdownParent: $('#depositModal'),
                        placeholder: 'Select Provider',
                        allowClear: false
                    });
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

    @can('Create Withdrawals')
        @include('money-point.transactions.withdraw-modal')
    @endcan

    @can('Create Deposits')
        @include('money-point.transactions.deposit-modal')
    @endcan

    @can('View Money Point Transactions')
        @include('money-point.transactions.show-modal')
    @endcan
@endsection
