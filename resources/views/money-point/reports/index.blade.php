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
                            <div class="d-flex justify-content-between mb-3">
                                <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                            </div>

                            <div class="contact-list radius-xl w-100">
                                @include('alerts.success')
                                @include('alerts.errors')

                                <div class="row">
                                    <!-- Shift Summary Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-calendar-alt text-primary me-2"></i>
                                                    Shift Summary Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    Generate a summary of shifts by date range, teller, and status.
                                                </p>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#shiftSummaryModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transaction Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-exchange-alt text-success me-2"></i>
                                                    Transaction Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    Detailed report of all transactions filtered by type, teller, and date range.
                                                </p>
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#transactionReportModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Float Balance Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-wallet text-info me-2"></i>
                                                    Float Balance Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    Current float balances by provider and teller.
                                                </p>
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#floatBalanceModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variance Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-exclamation-triangle text-warning me-2"></i>
                                                    Variance/Discrepancy Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    List of all shifts with discrepancies that need attention.
                                                </p>
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#varianceModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Daily Summary Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-chart-line text-danger me-2"></i>
                                                    Daily Summary Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    Daily cash flow summary with deposits, withdrawals, and shift statistics.
                                                </p>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#dailySummaryModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Teller Performance Report -->
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <i class="las la-user-tie text-secondary me-2"></i>
                                                    Teller Performance Report
                                                </h5>
                                                <p class="card-text text-muted small">
                                                    Performance metrics for each teller including transactions and shift completion.
                                                </p>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#tellerPerformanceModal">
                                                    Generate Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Summary Modal -->
    @include('money-point.reports.modals.shift-summary')

    <!-- Transaction Report Modal -->
    @include('money-point.reports.modals.transaction')

    <!-- Float Balance Modal -->
    @include('money-point.reports.modals.float-balance')

    <!-- Variance Modal -->
    @include('money-point.reports.modals.variance')

    <!-- Daily Summary Modal -->
    @include('money-point.reports.modals.daily-summary')

    <!-- Teller Performance Modal -->
    @include('money-point.reports.modals.teller-performance')
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for all select elements in modals
            $('.select2').select2({
                dropdownParent: $('.modal'),
                width: '100%'
            });
        });
    </script>
@endpush

