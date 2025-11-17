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

                                @if ($data['sms_settings_complete'])
                                    <div class="action-btn">
                                        <div class="drawer-btn d-flex justify-content-center">
                                            <button
                                                class="btn btn-primary btn-sm btn-default btn-squared btn-transparent-primary"
                                                data-bs-toggle="modal" data-bs-target="#sendSMSModal">
                                                Try Sending SMS
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="contact-list radius-xl w-100 mt-3">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')
                                @include('alerts.warning')

                                <form action="{{ route('sms-settings.update') }}" method="POST" id="smsSettingsForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="sms_api_token">SMS API TOKEN <span
                                                    style="color: red">*</span></label>
                                            <input type="text" name="sms_api_token" id="sms_api_token"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Enter SMS API TOKEN"
                                                value="{{ old('sms_api_token', $data['sms_api_token']) }}">
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <label for="sms_sender_id">SMS SENDER ID <span
                                                    style="color: red">*</span></label>
                                            <input type="text" name="sms_sender_id" id="sms_sender_id"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Enter Sender ID"
                                                value="{{ old('sms_sender_id', $data['sms_sender_id']) }}">
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <label for="sms_api_url">SMS API URL <span style="color: red">*</span></label>
                                            <input type="text" name="sms_api_url" id="sms_api_url"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                placeholder="Enter SMS API URL"
                                                value="{{ old('sms_api_url', $data['sms_api_url']) }}">
                                        </div>
                                    </div>

                                    <div class="layout-button">
                                        <button type="button" id="cancelBtn"
                                            class="btn btn-default btn-sm btn-squared btn-light px-20">Cancel
                                        </button>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary btn-sm btn-default btn-squared px-30">Save
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('sms-settings.send-sms')
@endsection

@push('page_scripts')
@endpush
