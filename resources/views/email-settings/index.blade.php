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

                                @if ($data['mail_settings_complete'])
                                    <div class="action-btn">
                                        <div class="drawer-btn d-flex justify-content-center">
                                            <button
                                                class="btn btn-primary btn-sm btn-default btn-squared btn-transparent-primary"
                                                data-bs-toggle="modal" data-bs-target="#sendMailModal">
                                                Try Sending Email
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="contact-list radius-xl w-100 mt-3">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <form action="{{ route('email-settings.update') }}" method="POST" id="emailSettingsForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="mail_mailer">Mail Mailer <span style="color: red">*</span></label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="mail_mailer" name="mail_mailer">
                                                <option value="smtp"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'smtp' ? 'selected' : '' }}>
                                                    SMTP</option>
                                                <option value="sendmail"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'sendmail' ? 'selected' : '' }}>
                                                    Sendmail</option>
                                                <option value="mailgun"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'mailgun' ? 'selected' : '' }}>
                                                    Mailgun</option>
                                                <option value="mandrill"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'mandrill' ? 'selected' : '' }}>
                                                    Mandrill</option>
                                                <option value="ses"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'ses' ? 'selected' : '' }}>
                                                    SES</option>
                                                <option value="sparkpost"
                                                    {{ old('mail_mailer', $data['mail_mailer']) === 'sparkpost' ? 'selected' : '' }}>
                                                    Sparkpost</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_host">Mail Host <span style="color: red">*</span></label>
                                            <input type="text" name="mail_host" id="mail_host"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail Host"
                                                value="{{ old('mail_host', $data['mail_host']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_port">Mail Port <span style="color: red">*</span></label>
                                            <input type="number" name="mail_port" id="mail_port"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail Port"
                                                value="{{ old('mail_port', $data['mail_port']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_username">Mail Username <span
                                                    style="color: red">*</span></label>
                                            <input type="text" name="mail_username" id="mail_username"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail Username"
                                                value="{{ old('mail_username', $data['mail_username']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_password">Mail Password <span
                                                    style="color: red">*</span></label>
                                            <input type="password" name="mail_password" id="mail_password"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail Password"
                                                value="{{ old('mail_password', $data['mail_password']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_encryption">Mail Encryption <span
                                                    style="color: red">*</span></label>
                                            <input type="text" name="mail_encryption" id="mail_encryption"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail Encryption"
                                                value="{{ old('mail_encryption', $data['mail_encryption']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_from_address">Mail From Address <span
                                                    style="color: red">*</span></label>
                                            <input type="email" name="mail_from_address" id="mail_from_address"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail From Address"
                                                value="{{ old('mail_from_address', $data['mail_from_address']) }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="mail_from_name">Mail From Name <span
                                                    style="color: red">*</span></label>
                                            <input type="text" name="mail_from_name" id="mail_from_name"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Please Enter Mail From Name"
                                                value="{{ old('mail_from_name', $data['mail_from_name']) }}">
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

    @include('email-settings.send-mail')
@endsection

@push('page_scripts')
@endpush
