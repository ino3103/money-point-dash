@extends('auth.app')

@section('content')
    <div class="card-header">
        <div class="edit-profile__title">
            <h6>Reset Password</h6>
        </div>
    </div>
    <div class="card-body">

        @include('alerts.email-error')
        @include('alerts.errors')

        <div class="edit-">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="input-list__single">
                    <div class="form-group mb-25">
                        <label>Email <span style="color: red">*</span></label>
                        <input class="form-control form-control-lg" type="email" name="email"
                            value="{{ old('email', $request->email) }}" placeholder="Enter email address" readonly>
                    </div>
                </div>

                <div class="input-list__single">
                    <div class="form-group mb-25">
                        <label for="password-field">Password <span style="color: red">*</span></label>
                        <div class="position-relative">
                            <input id="password-field" type="password" class="form-control form-control-lg" name="password"
                                placeholder="Enter password">
                            <span class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password2"></span>
                        </div>
                    </div>
                </div>

                <div class="input-list__single">
                    <div class="form-group mb-25">
                        <label for="password_confirmation">Confirm Password <span style="color: red">*</span></label>
                        <div class="position-relative">
                            <input id="password_confirmation" type="password" class="form-control form-control-lg"
                                name="password_confirmation" placeholder="Enter password">
                            <span class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password3"></span>
                        </div>
                    </div>
                </div>

                <div class="admin__button-group button-group d-flex justify-content-md-start justify-content-center pt-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-squared text-capitalize lh-normal px-50">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
