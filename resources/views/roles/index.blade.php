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

                                @can('Create Roles')
                                    <div class="action-btn">
                                        <div class="drawer-btn d-flex justify-content-center">
                                            <a href="{{ route('roles.create') }}"
                                                class="btn btn-primary btn-sm btn-default btn-squared btn-transparent-primary">
                                                New Role</a>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <div class="table-responsive table-responsive--dynamic">

                                    <table class="table-borderless table-rounded mb-0 table" id="roles-table">
                                        <thead>
                                            <tr>
                                                <th class="">S/n</th>
                                                <th class="">Name</th>
                                                @canany(['Edit Roles', 'Delete Roles'])
                                                    <th class="">Actions</th>
                                                @endcanany
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['roles'] as $index => $role)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        {{ $role->name }} 
                                                        <span class="badge bg-primary" title="PERMISSIONS">{{ $role->permissions_count }}</span>
                                                    </td>
                                                    @canany(['Edit Roles', 'Delete Roles'])
                                                        <td>
                                                            <div class="d-flex justify-content-aroundw">
                                                                @can('Edit Roles')
                                                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-xs btn-squared btn-transparent-warning">Edit</a>
                                                                @endcan
                                                                @can('Delete Roles')
                                                                    <button class="btn btn-danger btn-xs btn-squared btn-transparent-danger delete-role" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $role->id }}" data-name="{{ $role->name }}">Delete</button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    @endcanany
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
    @can('Delete Roles')
        @include('roles.delete')
    @endcan
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var defaultPageLength = {{ getSetting('default_page_length', 10) }};

            $('#roles-table').DataTable({
                pageLength: defaultPageLength,
                autoWidth: false,
                responsive: true,
                order: [[1, 'asc']]
            });
        });


        @can('Delete Roles')
            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var message = "Are you sure you want to delete Role '<strong>" + name + "</strong>'?";
                var modal = $(this);
                modal.find('.modal-body #nameToDelete').html(name); // Use .html() to render HTML tags
                modal.find('.modal-body #id').val(id);
            });
        @endcan
    </script>
@endpush
