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

                                @can('Create Users')
                                    <div class="action-btn">
                                        <div class="drawer-btn d-flex justify-content-center">
                                            <a href="{{ route('users.create') }}"
                                                class="btn btn-primary btn-sm btn-default btn-squared btn-transparent-primary">
                                                New User</a>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')
                                <div class="table-responsive table-responsive--dynamic">

                                    <table class="table-borderless table-rounded mb-0 table" id="users-table">
                                        <thead>
                                            <tr>
                                                <th class="">S/n</th>
                                                <th class="">Name</th>
                                                <th class="">Email</th>
                                                <th class="">Phone</th>
                                                <th class="">Role</th>
                                                <th class="">Status</th>
                                                @canany(['Edit Users', 'Delete Users', 'Change User Status'])
                                                    <th class="">Actions</th>
                                                @endcanany
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['users'] as $index => $user)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="contact-item d-flex align-items-center">
                                                            <div class="contact-personal-info d-flex">
                                                                @php
                                                                    $profilePicture = $user->profile_picture ? asset($user->profile_picture) : asset('assets/img/avatar.png');
                                                                @endphp
                                                                <a href="#" class="profile-image rounded-circle d-block wh-38 m-0" style="background-image: url('{{ $profilePicture }}'); background-size: cover;"></a>
                                                                <div class="contact_title">
                                                                    <h6><a href="#">{{ $user->name }}</a></h6>
                                                                    <span class="location">{{ $user->username }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{!! $user->phone_no ?: '<i>N/L</i>' !!}</td>
                                                    <td>
                                                        <div class="userDatatable-content d-inline-block">
                                                            <span class="bg-opacity-primary color-primary userDatatable-content-status active">
                                                                {{ $user->roles->isNotEmpty() ? $user->roles->pluck('name')->implode(', ') : 'N/L' }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = $user->status == 1 ? 'active' : 'inactive';
                                                            $status = ucwords($status);
                                                            $bgClass = $user->status == 1 ? 'success' : 'danger';
                                                        @endphp
                                                        <div class="userDatatable-content d-inline-block">
                                                            <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $status }}</span>
                                                        </div>
                                                    </td>
                                                    @canany(['Edit Users', 'Delete Users', 'Change User Status'])
                                                        <td>
                                                            <div class="d-flex justify-content-around">
                                                                @can('Change User Status')
                                                                    @php
                                                                        $statusBtnText = $user->status ? 'Deactivate' : 'Activate';
                                                                        $statusBtnClass = $user->status ? 'btn-danger btn-transparent-danger' : 'btn-success btn-transparent-success';
                                                                    @endphp
                                                                    <button class="btn {{ $statusBtnClass }} btn-xs btn-squared toggle-status" data-id="{{ $user->id }}">{{ $statusBtnText }}</button>
                                                                @endcan
                                                                @can('Edit Users')
                                                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-xs btn-squared btn-transparent-warning">Edit</a>
                                                                @endcan
                                                                @can('Delete Users')
                                                                    <button class="btn btndanger btn-transparent-danger btn-xs btn-squared" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $user->id }}" data-name="{{ $user->name }}">Delete</button>
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
    @can('Delete Users')
        @include('users.delete')
    @endcan
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var defaultPageLength = {{ getSetting('default_page_length', 10) }};

            $('#users-table').DataTable({
                pageLength: defaultPageLength,
                autoWidth: false,
                responsive: true,
                order: [[1, 'asc']]
            });
        });

        @can('Change User Status')
            $(document).on('click', '.toggle-status', function() {
                var userId = $(this).data('id');
                var $button = $(this);

                $.ajax({
                    url: '/users/' + userId + '/toggle-status',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            var newStatus = response.newStatus;
                            $button.text(newStatus ? 'Deactivate' : 'Activate')
                                .toggleClass('btn-success btn-danger');
                            location.reload();
                            alert(response.message);
                        }
                    },
                    error: function(response) {
                        alert('Error updating user status');
                    }
                });
            });
        @endcan


        @can('Delete Users')
            $('#deleteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var message = "Are you sure you want to delete User '<strong>" + name + "</strong>'?";
                var modal = $(this);
                modal.find('.modal-body #userNameToDelete').html(name); // Use .html() to render HTML tags
                modal.find('.modal-body #id').val(id);
            });
        @endcan
    </script>
@endpush
