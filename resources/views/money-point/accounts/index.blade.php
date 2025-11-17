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
                            <div class="d-flex justify-content-between mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">
                                <div class="table-responsive table-responsive--dynamic">
                                    <table class="table-borderless table-rounded mb-0 table" id="accounts-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Type</th>
                                                <th>Provider</th>
                                                <th>Display Balance</th>
                                                <th>System Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($accounts as $index => $account)
                                                <tr>
                                                    <td>{{ $account->id }}</td>
                                                    <td>{{ $account->user ? $account->user->name : 'System' }}</td>
                                                    <td>
                                                        @php
                                                            $typeText = ucwords($account->account_type);
                                                            $bgClass = match ($account->account_type) {
                                                                'cash' => 'success',
                                                                'float' => 'primary',
                                                                'bank' => 'info',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <div class="userDatatable-content d-inline-block">
                                                            <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $typeText }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($account->provider && $account->provider !== 'cash')
                                                            @php
                                                                $providerModel = \App\Models\FloatProvider::where('name', $account->provider)->first();
                                                                $displayName = $providerModel ? $providerModel->display_name : ucfirst($account->provider);
                                                            @endphp
                                                            <div class="userDatatable-content d-inline-block">
                                                                <span class="bg-opacity-primary color-primary userDatatable-content-status active">{{ $displayName }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($account->account_type === 'float')
                                                            {{ formatCurrency(abs($account->balance), 0) }}
                                                        @else
                                                            {{ formatCurrency($account->balance, 0) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ formatCurrency($account->balance, 0) }}</td>
                                                    <td>
                                                        @can('View Ledger')
                                                            <button type="button" class="btn btn-primary btn-xs view-ledger" data-id="{{ $account->id }}" data-bs-toggle="modal" data-bs-target="#ledgerModal">Ledger</button>
                                                        @endcan
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
            if ($.fn.DataTable.isDataTable('#accounts-table')) {
                $('#accounts-table').DataTable().destroy();
            }

            var defaultPageLength = {{ getSetting('default_page_length', 10) }};
            $('#accounts-table').DataTable({
                pageLength: defaultPageLength,
                order: [[0, 'desc']] // Sort by ID descending
            });

            // Load ledger when modal is opened
            $('#ledgerModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var accountId = button.data('id');
                var modal = $(this);
                
                // Reset content to loading state
                modal.find('#ledgerContent').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading ledger...</p>
                    </div>
                `);
                
                // Load ledger via AJAX
                $.ajax({
                    url: "{{ route('money-point.accounts.ledger', ':id') }}".replace(':id', accountId),
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        modal.find('#ledgerContent').html(response.html);
                        // Initialize DataTable after content is loaded
                        if ($.fn.DataTable.isDataTable('#ledger-table-modal')) {
                            $('#ledger-table-modal').DataTable().destroy();
                        }
                        var defaultPageLength = {{ getSetting('default_page_length', 10) }};
                        $('#ledger-table-modal').DataTable({
                            pageLength: defaultPageLength,
                            order: [[0, 'desc']] // Sort by date descending
                        });
                    },
                    error: function(xhr) {
                        modal.find('#ledgerContent').html(`
                            <div class="alert alert-danger border-0">
                                <i class="las la-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> Failed to load ledger. Please try again.
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
@endpush

@can('View Ledger')
    @include('money-point.accounts.ledger-modal')
@endcan
@endsection

