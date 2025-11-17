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

                            <div class="d-flex justify-content-between mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>

                                @can('Create Accounts')
                                    <div class="action-btn">
                                        <div class="drawer-btn d-flex justify-content-center">
                                            <button data-bs-toggle="modal" data-bs-target="#createModal"
                                                class="btn btn-primary btn-sm btn-default btn-squared btn-transparent-primary">
                                                New Float Provider</button>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')
                                <div class="table-responsive table-responsive--dynamic">

                                    <table class="table-borderless table-rounded mb-0 table" id="float-providers-table">
                                        <thead>
                                            <tr>
                                                <th class="">S/n</th>
                                                <th class="">Name</th>
                                                <th class="">Display Name</th>
                                                <th class="">Description</th>
                                                <th class="">Sort Order</th>
                                                <th class="">Status</th>
                                                @can('Create Accounts')
                                                    <th class="">Actions</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($providers as $index => $provider)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $provider->name }}</td>
                                                    <td>{{ $provider->display_name }}</td>
                                                    <td>{{ $provider->description ?? '-' }}</td>
                                                    <td>{{ $provider->sort_order }}</td>
                                                    <td>
                                                        @php
                                                            $status = $provider->is_active ? 'Active' : 'Inactive';
                                                            $bgClass = $provider->is_active ? 'success' : 'secondary';
                                                        @endphp
                                                        <div class="userDatatable-content d-inline-block">
                                                            <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ ucwords($status) }}</span>
                                                        </div>
                                                    </td>
                                                    @can('Create Accounts')
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-warning btn-xs btn-squared btn-transparent-warning" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $provider->id }}" data-name="{{ $provider->name }}" data-display_name="{{ $provider->display_name }}" data-description="{{ htmlspecialchars($provider->description ?? '') }}" data-is_active="{{ $provider->is_active ? 1 : 0 }}" data-sort_order="{{ $provider->sort_order }}">Edit</button>
                                                                <button class="btn btn-{{ $provider->is_active ? 'secondary' : 'success' }} btn-xs btn-squared" onclick="toggleProvider({{ $provider->id }}, {{ $provider->is_active ? 0 : 1 }})">{{ $provider->is_active ? 'Disable' : 'Enable' }}</button>
                                                            </div>
                                                        </td>
                                                    @endcan
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

    @can('Create Accounts')
        <!-- Create Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Create Float Provider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('money-point.float-providers.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name (Code) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="e.g., mpesa, tigopesa">
                                <small class="text-muted">Lowercase, no spaces (used as identifier)</small>
                            </div>
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="display_name" name="display_name" required
                                    placeholder="e.g., M-Pesa, Tigo Pesa">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Float Provider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('money-point.float-providers.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Name (Code) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <small class="text-muted">Lowercase, no spaces (used as identifier)</small>
                            </div>
                            <div class="mb-3">
                                <label for="edit_display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_display_name" name="display_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                    <label class="form-check-label" for="edit_is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var defaultPageLength = {{ getSetting('default_page_length', 10) }};

            $('#float-providers-table').DataTable({
                pageLength: defaultPageLength,
                autoWidth: false,
                responsive: true,
                order: [[4, 'asc'], [2, 'asc']] // Sort by sort_order, then display_name
            });

            @can('Create Accounts')
                $('#editModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var name = button.data('name');
                    var display_name = button.data('display_name');
                    var description = button.data('description');
                    var is_active = button.data('is_active');
                    var sort_order = button.data('sort_order');
                    var modal = $(this);
                    modal.find('#edit_id').val(id);
                    modal.find('#edit_name').val(name);
                    modal.find('#edit_display_name').val(display_name);
                    modal.find('#edit_description').val(description);
                    modal.find('#edit_sort_order').val(sort_order);
                    modal.find('#edit_is_active').prop('checked', is_active == 1);
                });
            @endcan
        });

        function toggleProvider(id, newStatus) {
            if (confirm('Are you sure you want to ' + (newStatus == 1 ? 'enable' : 'disable') + ' this provider?')) {
                $.ajax({
                    url: '/money-point/float-providers/' + id + '/toggle',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    }
                });
            }
        }
    </script>
@endpush

