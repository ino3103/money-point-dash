@extends('auth.app')

@section('content')
    <div class="card-header">
        <div class="edit-profile__title">
            <h6>Sign</h6>
        </div>
    </div>
    <div class="card-body">

        @include('alerts.email-error')
        @include('alerts.status')

        <div class="edit-">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-list__single">
                    <div class="form-group mb-25">
                        <label>Email <span style="color: red">*</span></label>
                        <input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}"
                            placeholder="Enter email address">
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

                <div class="admin-condition">
                    <div class="checkbox-theme-default custom-checkbox">
                        <input class="checkbox" type="checkbox" id="check-1">
                        <label for="check-1">
                            <span class="checkbox-text">Keep me logged in</span>
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <div class="admin__button-group button-group d-flex justify-content-md-start justify-content-center pt-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-squared text-capitalize lh-normal px-50">
                        <img src="{{ asset('assets/img/svg/log-in.svg') }}" alt="login" class="svg">
                        sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
