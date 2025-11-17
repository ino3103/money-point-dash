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

                                <div class="action-btn">
                                    <div class="drawer-btn d-flex justify-content-center">
                                        <a href="{{ route('roles.index') }}"
                                            class="btn btn-primary btn-sm btn-default btn-squared" data-drawer="account">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <form action="{{ route('roles.store') }}" method="POST" id="userForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="name">Role Name <span style="color: red">*</span></label>
                                            <input type="text" name="name" id="name"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Role Name" value="{{ old('name') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group row">
                                            <label for="" class="form-label">Permisions<font color="red">*
                                                </font>
                                            </label>
                                            <div class="col-md-12">
                                                <div class="form-group row mb-2">
                                                    <div class="col-sm-6">
                                                        <label class="checkbox">
                                                            <input type="checkbox" id="check_all">
                                                            <span class="mr-2"> </span> Check All
                                                        </label>
                                                    </div>
                                                </div>
                                                {!! $errors->first('permissions', '<p class="error_message">:message</p>') !!}

                                                <div class="card card-dashed">
                                                    @foreach ($permissions as $key => $item)
                                                        <div class="card-header" style="background-color: #F4F7FA">
                                                            <h3 class="card-title">{{ $key }}</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                @foreach ($item as $permission)
                                                                    <div class="col-sm-4 mb-2">
                                                                         <div class="checkbox-theme-default custom-checkbox">
                                                                            <input class="checkbox" type="checkbox" name="permissions[]"
                                                                                id="{{ $permission->id }}" value="{{ $permission->id }}">
                                                                            <label for="{{ $permission->id }}">
                                                                                <span class="checkbox-text">
                                                                                    {{ $permission->name }}
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="layout-button">
                                        <button type="button" id="cancelBtn"
                                            class="btn btn-default btn-sm btn-squared btn-light px-20">cancel</button>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary btn-sm btn-default btn-squared px-30">save</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#check_all').click(function() {
                var c = this.checked;
                $(':checkbox').prop('checked', c);
            });
        });

        $(document).ready(function() {
            // Clear all inputs when cancel button is clicked
            $('#cancelBtn').click(function() {
                $('input[type="text"]').val('');
                $('#email_error').hide();
                $('#submitBtn').prop('disabled', true);
            });

            function validateForm() {
                let isValid = true;

                // Force inputs to start with a capital letter except for email and phone number
                $('input[type="text"]').each(function() {
                    let start = $(this).val().charAt(0).toUpperCase();
                    let rest = $(this).val().slice(1);
                    $(this).val(start + rest);
                });

                // Enable or disable the submit button based on form validity
                $('#submitBtn').prop('disabled', !isValid);
            }

            // Attach the validation function to input events
            $('input').on('input', validateForm);
            $('input').on('blur', showValidationErrors);
            $('select').on('change', validateForm);

            // Validate the form on page load
            validateForm();
        });
    </script>
@endpush
