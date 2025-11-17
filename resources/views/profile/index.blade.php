@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                @include('alerts.success')
                @include('alerts.errors')
                @include('alerts.error')

                <div class="row">
                    <div class="col-xxl-3 col-md-4">
                        <aside class="profile-sider">

                            <div class="card mb-25">
                                <div class="card-body pt-sm-30 pb-sm-0 px-25 pb-0 text-center">
                                    <div class="account-profile">
                                        <div class="ap-img w-100 d-flex justify-content-center">
                                            <img class="ap-img__main rounded-circle wh-120 d-flex bg-opacity-primary mb-3"
                                                src="{{ asset('assets/img/avatar.png') }}" alt="profile">
                                        </div>
                                        <div class="ap-nameAddress pb-3 pt-1">
                                            <h5 class="ap-nameAddress__title">{{ Auth::user()->name }}</h5>
                                            @foreach (Auth::user()->roles as $role)
                                                <p class="ap-nameAddress__subTitle fs-14 m-0"><b>Role: &nbsp;</b>
                                                    {{ $role->name }}</p>
                                            @endforeach
                                            <p class="ap-nameAddress__subTitle fs-14 m-0"><b>Gender: &nbsp;</b>
                                                {{ Auth::user()->gender }}</p>
                                            <p class="ap-nameAddress__subTitle fs-14 m-0"><b>UserName: &nbsp;</b>
                                                {{ Auth::user()->username }}</p>
                                            <p class="ap-nameAddress__subTitle fs-14 m-0"><b>Email: &nbsp;</b>
                                                {{ Auth::user()->email }}</p>
                                            <p class="ap-nameAddress__subTitle fs-14 m-0"><b>Phone No: &nbsp;</b>
                                                {{ Auth::user()->phone_no }}</p>
                                            @php
                                                $createdAt = Auth::user()->created_at;
                                                $dateFormat = getSetting('date_format', 'Y-m-d');
                                                $timeFormat = getSetting('time_format', 'H:i:s');
                                                $formattedDate = Carbon\Carbon::parse($createdAt)->format(
                                                    "$dateFormat $timeFormat",
                                                );
                                            @endphp
                                            <p class="ap-nameAddress__subTitle fs-14 m-0">
                                                <b>Reg Date: &nbsp;</b> {{ $formattedDate }}
                                            </p>
                                        </div>
                                        <div class="ap-button button-group d-flex justify-content-center mb-3 flex-wrap">
                                            @can('Edit Own Details')
                                                <button type="button"
                                                    class="text-capitalize px-25 btn btn-primary btn-squared px-25"
                                                    data-bs-toggle="modal" data-bs-target="#updateDetailsModal">
                                                    Update Details
                                                </button>
                                            @endcan

                                            @can('Change Password')
                                                <button type="button" class="btn btn-primary btn-squared text-capitalize px-25"
                                                    data-bs-toggle="modal" data-bs-target="#updatePasswordModal">
                                                    Update Password
                                                </button>
                                            @endcan

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </aside>
                    </div>
                    <div class="col-xxl-9 col-md-8">
                        <div class="ap-tab ap-tab-header">
                            <div class="ap-tab-wrapper">
                                <ul class="nav px-25 ap-tab-main" id="ap-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="ap-overview-tab" data-bs-toggle="pill"
                                            href="#ap-overview" role="tab" aria-selected="true">Login Histories</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="timeline-tab" data-bs-toggle="pill" href="#timeline"
                                            role="tab" aria-selected="false">Activities</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content mt-25" id="ap-tabContent">
                            <div class="tab-pane fade show active" id="ap-overview" role="tabpanel"
                                aria-labelledby="ap-overview-tab">
                                <div class="ap-content-wrapper">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card mb-40 mt-20">
                                                <div class="card-header text-capitalize px-md-25 px-3">
                                                    <h6>My Login Histories</h6>
                                                </div>
                                                <div class="card-body p-4">
                                                    <div class="ap-product">
                                                        <div class="table-responsive">
                                                            <table class="table-borderless table-rounded mb-0 table"
                                                                id="loginHistoryTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>User</th>
                                                                        <th>IP Address</th>
                                                                        <th>User Agent</th>
                                                                        <th>Login At</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($data['loginHistories'] as $index => $login)
                                                                        <tr>
                                                                            <td>{{ $index + 1 }}</td>
                                                                            <td>
                                                                                @if($login->user)
                                                                                    {{ $login->user->name }} ({{ $login->user->username }})
                                                                                @else
                                                                                    N/A
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $login->ip_address }}</td>
                                                                            <td>{{ $login->user_agent }}</td>
                                                                            <td>
                                                                                @php
                                                                                    $dateFormat = getSetting('date_format', 'Y-m-d');
                                                                                    $timeFormat = getSetting('time_format', 'H:i:s');
                                                                                    $formattedDate = $login->login_at ? Carbon\Carbon::parse($login->login_at)->format("$dateFormat $timeFormat") : 'N/A';
                                                                                @endphp
                                                                                {{ $formattedDate }}
                                                                            </td>
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
                            <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                                <div class="ap-post-content">
                                    <div class="row">
                                        <div class="col-xxl-12">

                                            <div class="ap-post-form">
                                                <div class="card mb-25 border-0">
                                                    <div class="card-header px-md-25 px-3">
                                                        <h6>My Activities</h6>
                                                    </div>
                                                    <div class="card-body px-25 p-0">
                                                        <div class="d-flex flex-column">
                                                            <div class="position-relative flex-1 border-0">
                                                                <div class="rounded-0 position-relative border-bottom pb-2 pe-0 ps-0 pt-20 outline-0"
                                                                    tabindex="-1">
                                                                    <span
                                                                        class="ap-profile-image bg-opacity-secondary rounded-circle d-block position-absolute"
                                                                        style="background-image:url('img/ap-author.png'); background-size: cover;"></span>
                                                                    <div class="ps-15 ms-50 pt-10">
                                                                        <textarea class="form-control fs-xl border-0 bg-transparent p-0" rows="3" placeholder="Write something..."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="ap-post-attach d-flex align-items-center flex-shrink-0 flex-row flex-wrap">
                                                                <a href="#" class="btn rounded-pill me-2">
                                                                    <img class="svg"
                                                                        src="{{ asset('assets/img/svg/image.svg') }}"
                                                                        alt="img">
                                                                    Photo/Video
                                                                </a>
                                                                <a href="#"
                                                                    class="btn rounded-pill ap-post-attach__drop">
                                                                    <img src="{{ asset('assets/img/svg/more-horizontal.svg') }}"
                                                                        alt="more-horizontal" class="svg">
                                                                </a>
                                                                <button
                                                                    class="btn btn-primary btn-default btn-squared ap-post-attach__btn ms-auto">public
                                                                    post
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('Edit Own Details')
        @include('profile.update-details')
    @endcan

    @can('Change Password')
        @include('profile.update-password')
    @endcan
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var defaultPageLength = {{ getSetting('default_page_length', 10) }};

            $('#loginHistoryTable').DataTable({
                pageLength: defaultPageLength,
                autoWidth: false,
                responsive: true,
                order: [[4, 'desc']] // Sort by login_at descending
            });
        });
    </script>
@endpush
