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
                                        <a href="{{ route('users.index') }}"
                                            class="btn btn-primary btn-sm btn-default btn-squared" data-drawer="account">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')

                                <form action="{{ route('users.store') }}" method="POST" id="userForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="full_name">Full Name <span style="color: red">*</span></label>
                                            <input type="text" name="full_name" id="full_name"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Full Name" value="{{ old('full_name') }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="email">Email <span style="color: red">*</span></label>
                                            <input type="email" name="email" id="email"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Email Address" value="{{ old('email') }}">
                                            <small id="email_error" class="text-danger" style="display: none;">Invalid email
                                                address</small>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="phone_no">Phone No <span style="color: red">*</span></label>
                                            <input type="number" name="phone_no" id="phone_no"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Phone Number" value="{{ old('phone_no') }}">
                                            <small id="phone_no_error" class="text-danger" style="display: none;">Phone
                                                number must start with 255 and be no longer than 12 digits</small>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="username">Username <span style="color: red">*</span></label>
                                            <input type="text" name="username" id="username"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Username" value="{{ old('username') }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="gender">Gender <span style="color: red">*</span></label>
                                            <select name="gender" id="gender"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="role_id">Role <span style="color: red">*</span></label>
                                            <select name="role_id" id="role_id"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                @foreach ($data['roles'] as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('role_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="layout-button">
                                        <button type="button" id="cancelBtn"
                                            class="btn btn-default btn-sm btn-squared btn-light px-20">cancel</button>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary btn-sm btn-default btn-squared px-30"
                                            disabled>save</button>
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
            // Clear all inputs when cancel button is clicked
            $('#cancelBtn').click(function() {
                $('input[type="text"], input[type="email"], input[type="number"]').val('');
                $('select').prop('selectedIndex', 0);
                $('#email_error').hide();
                $('#phone_no_error').hide();
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

                // Ensure email is in lowercase and valid
                let email = $('#email').val().toLowerCase();
                $('#email').val(email);
                let emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
                if (!emailPattern.test(email)) {
                    $('#email_error').show();
                    isValid = false;
                } else {
                    $('#email_error').hide();
                }

                // Validate phone number to start with 255 and be no longer than 12 digits
                let phoneNo = $('#phone_no').val();
                if (!phoneNo.startsWith('255') || phoneNo.length > 12) {
                    $('#phone_no_error').show();
                    isValid = false;
                } else {
                    $('#phone_no_error').hide();
                }

                // Enable or disable the submit button based on form validity
                $('#submitBtn').prop('disabled', !isValid);
            }

            function showValidationErrors() {
                // Ensure email is in lowercase and valid
                let email = $('#email').val().toLowerCase();
                $('#email').val(email);
                let emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
                if (!emailPattern.test(email)) {
                    $('#email_error').show();
                } else {
                    $('#email_error').hide();
                }

                // Validate phone number to start with 255 and be no longer than 12 digits
                let phoneNo = $('#phone_no').val();
                if (!phoneNo.startsWith('255') || phoneNo.length > 12) {
                    $('#phone_no_error').show();
                } else {
                    $('#phone_no_error').hide();
                }
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
