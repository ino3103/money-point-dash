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

                    <div class="col-12">
                        <div class="contact-list-wrap mb-25">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title mb-0">{{ $data['title'] }}</h4>
                                </div>
                                @can('Open Shifts')
                                    <div class="action-btn">
                                        <a href="{{ route('money-point.shifts.create') }}" class="btn btn-primary btn-sm">
                                            <i class="las la-plus-circle me-1"></i> New Shift
                                        </a>
                                    </div>
                                @endcan
                            </div>

                            <div class="contact-list radius-xl w-100">
                                <div class="table-responsive table-responsive--dynamic">
                                    <table class="table-borderless table-rounded mb-0 table" id="shifts-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Teller</th>
                                                <th>Treasurer</th>
                                                <th>Opening Cash</th>
                                                <th>Status</th>
                                                <th>Opened At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shifts as $index => $shift)
                                                <tr>
                                                    <td>{{ $shift->id }}</td>
                                                    <td>{{ $shift->teller ? $shift->teller->name : 'N/A' }}</td>
                                                    <td>{{ $shift->treasurer ? $shift->treasurer->name : 'N/A' }}</td>
                                                    <td>{{ formatCurrency($shift->opening_cash, 0) }}</td>
                                                    <td>
                                                        @php
                                                            $status = ucwords($shift->status);
                                                            $bgClass = match ($shift->status) {
                                                                'open' => 'primary',
                                                                'submitted' => 'warning',
                                                                'verified' => 'success',
                                                                'closed' => 'secondary',
                                                                'discrepancy' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <div class="userDatatable-content d-inline-block">
                                                            <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $status }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($shift->opened_at)
                                                            @php
                                                                $dateFormat = getSetting('date_format', 'Y-m-d');
                                                                $timeFormat = getSetting('time_format', 'H:i:s');
                                                            @endphp
                                                            {{ \Carbon\Carbon::parse($shift->opened_at)->format("$dateFormat $timeFormat") }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('money-point.shifts.show', $shift->id) }}" class="btn btn-primary btn-xs">View</a>
                                                        </div>
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
                if ($.fn.DataTable.isDataTable('#shifts-table')) {
                    $('#shifts-table').DataTable().destroy();
                }

                var defaultPageLength = {{ getSetting('default_page_length', 10) }};
                var table = $('#shifts-table').DataTable({
                    pageLength: defaultPageLength,
                    order: [[5, 'desc']] // Sort by opened_at descending
                });
            });
        </script>
    @endpush
@endsection
