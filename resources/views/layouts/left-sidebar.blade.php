<div class="sidebar-wrapper">
    <div class="sidebar sidebar-collapse" id="sidebar">
        <div class="sidebar__menu-group">
            <ul class="sidebar_nav">
                @php
                    $moneyPointRoutes = [
                        'money-point.index',
                        'money-point.shifts',
                        'money-point.shifts.create',
                        'money-point.shifts.show',
                        'money-point.accounts',
                        'money-point.accounts.ledger',
                        'money-point.float-providers',
                        'money-point.transactions',
                        'money-point.transactions.show',
                        'money-point.transactions.withdraw.create',
                        'money-point.transactions.deposit.create',
                        'money-point.reports',
                    ];
                @endphp

                @can('View Money Point Module')
                    <li class="has-child {{ isActiveRoute($moneyPointRoutes, 'open') }}">
                        <a href="#" class="{{ isActiveRoute($moneyPointRoutes) }}">
                            <span class="nav-icon uil las la-cash-register"></span>
                            <span class="menu-text">MONEY POINT</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li class="{{ isActiveRoute(['money-point.index', 'dashboard']) }}">
                                <a href="{{ route('money-point.index') }}">DASHBOARD</a>
                            </li>
                            @can('View Shifts')
                                <li
                                    class="{{ isActiveRoute(['money-point.shifts', 'money-point.shifts.create', 'money-point.shifts.show']) }}">
                                    <a href="{{ route('money-point.shifts') }}">SHIFTS</a>
                                </li>
                            @endcan
                            @can('View Accounts')
                                <li class="{{ isActiveRoute(['money-point.accounts', 'money-point.accounts.ledger']) }}">
                                    <a href="{{ route('money-point.accounts') }}">ACCOUNTS</a>
                                </li>
                                <li class="{{ isActiveRoute(['money-point.float-providers']) }}">
                                    <a href="{{ route('money-point.float-providers') }}">FLOAT PROVIDERS</a>
                                </li>
                            @endcan
                            @can('View Money Point Transactions')
                                <li
                                    class="{{ isActiveRoute(['money-point.transactions', 'money-point.transactions.show', 'money-point.transactions.withdraw.create', 'money-point.transactions.deposit.create']) }}">
                                    <a href="{{ route('money-point.transactions') }}">TRANSACTIONS</a>
                                </li>
                            @endcan
                            @can('View Money Point Reports')
                                <li class="{{ isActiveRoute(['money-point.reports']) }}">
                                    <a href="{{ route('money-point.reports') }}">REPORTS</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('View Users')
                    <li class="has-child {{ isActiveRoute(['users.index', 'users.create', 'users.edit'], 'open') }}">
                        <a href="#" class="{{ isActiveRoute(['users.index', 'users.create', 'users.edit']) }}">
                            <span class="nav-icon uil las la-user-friends"></span>
                            <span class="menu-text">USERS</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li class="{{ isActiveRoute(['users.index']) }}">
                                <a href="{{ route('users.index') }}">ALL USERS</a>
                            </li>
                            @can('Create Users')
                                <li class="{{ isActiveRoute(['users.create']) }}">
                                    <a href="{{ route('users.create') }}">ADD USER</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('View Roles')
                    <li class="has-child {{ isActiveRoute(['roles.index', 'roles.create', 'roles.edit'], 'open') }}">
                        <a href="#" class="{{ isActiveRoute(['roles.index', 'roles.create', 'roles.edit']) }}">
                            <span class="nav-icon uil las la-user-shield"></span>
                            <span class="menu-text">ROLES</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li class="{{ isActiveRoute(['roles.index']) }}">
                                <a href="{{ route('roles.index') }}">ALL ROLES</a>
                            </li>
                            @can('Create Roles')
                                <li class="{{ isActiveRoute(['roles.create']) }}">
                                    <a href="{{ route('roles.create') }}">ADD ROLE</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('View Settings Module')
                    <li class="has-child {{ isActiveRoute(['settings.index', 'email-settings', 'sms-settings'], 'open') }}">
                        <a href="#" class="{{ isActiveRoute(['settings.index', 'email-settings', 'sms-settings']) }}">
                            <span class="nav-icon uil las la-cogs"></span>
                            <span class="menu-text">SETTINGS</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('View System Settings')
                                <li class="{{ isActiveRoute(['settings.index']) }}">
                                    <a href="{{ route('settings.index') }}">SYSTEM SETTINGS</a>
                                </li>
                            @endcan
                            @can('View Email Settings')
                                <li class="{{ isActiveRoute(['email-settings']) }}">
                                    <a href="{{ route('email-settings') }}">EMAIL SETTINGS</a>
                                </li>
                            @endcan
                            @can('View SMS Settings')
                                <li class="{{ isActiveRoute(['sms-settings']) }}">
                                    <a href="{{ route('sms-settings') }}">SMS SETTINGS</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <li class="{{ isActiveRoute(['profile.index']) }}">
                    <a href="{{ route('profile.index') }}">
                        <span class="nav-icon uil las la-user"></span>
                        <span class="menu-text">PROFILE</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
