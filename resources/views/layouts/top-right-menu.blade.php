<div class="navbar-right">
    <ul class="navbar-right__menu">
        <li class="nav-search">
            <a href="#" class="search-toggle">
                <i class="uil uil-search"></i>
                <i class="uil uil-times"></i>
            </a>
            <form action="#" class="search-form-topMenu">
                <span class="search-icon uil uil-search"></span>
                <input class="form-control me-sm-2 box-shadow-none" type="search" placeholder="Search..."
                    aria-label="Search">
            </form>
        </li>

        <li class="nav-notification">
            <div class="dropdown-custom">
                <a href="javascript:;" class="nav-item-toggle icon-active">
                    <img class="svg" src="{{ asset('assets/img/svg/alarm.svg') }}" alt="img">
                </a>

                <div class="dropdown-parent-wrapper">
                    <div class="dropdown-wrapper">
                        <h2 class="dropdown-wrapper__title">Notifications
                            <span id="totalPendingBadgeTitle" class="badge-circle badge-warning ms-1"></span>
                        </h2>
                        <ul class="notification-list">
                            <li id="pendingIncomesNotification"
                                class="nav-notification__single nav-notification__single--unread d-flex flex-wrap"></li>
                            <li id="pendingExpensesNotification"
                                class="nav-notification__single nav-notification__single--unread d-flex flex-wrap"></li>
                            <li id="pendingTransactionsNotification"
                                class="nav-notification__single nav-notification__single--unread d-flex flex-wrap"></li>
                        </ul>
                        {{-- <a href="#" class="dropdown-wrapper__more">See all incoming
                            activity</a> --}}
                    </div>
                </div>
            </div>
        </li>

        <li class="nav-flag-select">
            <div class="dropdown-custom">
                <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/flag.png') }}" alt
                        class="rounded-circle"></a>
                <div class="dropdown-parent-wrapper">
                    <div class="dropdown-wrapper dropdown-wrapper--small">
                        <a href><img src="{{ asset('assets/img/eng.png') }}" alt> English</a>
                    </div>
                </div>
            </div>
        </li>

        @auth
            <li class="nav-author">
                <div class="dropdown-custom">
                    <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/avatar.png') }}" alt
                            class="rounded-circle">
                        <span class="nav-item__title">{{ Auth::user()->username ?? 'User' }}<i
                                class="las la-angle-down nav-item__arrow"></i></span>
                    </a>
                    <div class="dropdown-parent-wrapper">
                        <div class="dropdown-wrapper">
                            <div class="nav-author__info">
                                <div class="author-img">
                                    <img src="{{ asset('assets/img/avatar.png') }}" alt class="rounded-circle">
                                </div>
                                <div>
                                    <h6>{{ Auth::user()->name ?? 'User' }}</h6>
                                    <span>
                                        @if (Auth::user() && Auth::user()->roles)
                                            @foreach (Auth::user()->roles as $role)
                                                <p>{{ $role->name }}</p>
                                            @endforeach
                                        @endif
                                    </span>

                                </div>

                            </div>
                            <div class="nav-author__options">
                                <ul>
                                    <li>
                                        <a href ="{{ route('profile.index') }}">
                                            <i class="uil uil-user"></i> Profile</a>
                                    </li>
                                    <li>
                                        <a href>
                                            <i class="uil uil-users-alt"></i> Activity</a>
                                    </li>
                                </ul>
                                <a class="nav-author__signout" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="uil uil-sign-out-alt"></i> <span key="t-logout">Sign Out</span></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </li>
        @endauth

    </ul>

    <div class="navbar-right__mobileAction d-md-none">
        <a href="#" class="btn-search">
            <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg feather-search">
            <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg feather-x"></a>
        <a href="#" class="btn-author-action">
            <img class="svg') }}" src="{{ asset('assets/img/svg/more-vertical.svg') }}" alt="more-vertical"></a>
    </div>
</div>
