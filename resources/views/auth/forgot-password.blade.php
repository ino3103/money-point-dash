@extends('auth.app')

@section('content')
    <div class="card-header">
        <div class="edit-profile__title fw-500 text-center">
            {{ __('Forgot your password? No problem. Just let us know your email address.') }}
        </div>
    </div>
    <div class="card-body">

        @include('alerts.errors')
        @include('alerts.status')

        <div class="edit-">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-list__single">
                    <div class="form-group mb-25">
                        <label>Email <span style="color: red">*</span></label>
                        <input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}"
                            placeholder="Enter email address">
                    </div>
                </div>
                <div class="admin__button-group button-group d-flex justify-content-md-start justify-content-center pt-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 btn-squared text-capitalize lh-normal px-50">
                        <img src="{{ asset('assets/img/svg/log-in.svg') }}" alt="login" class="svg">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
