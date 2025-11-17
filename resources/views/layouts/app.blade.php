<!doctype html>
<html lang="en" dir="ltr">
@php
    $siteName = getSetting('site_name', env('APP_NAME'));
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $siteName }} | {{ $data['title'] ?? '' }}</title>
    <link href="{{ asset('fonts/css2a324.css?family=Jost:wght@400;500;600;700&amp;display=swap') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/plugin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/datatable/dataTables.bootstrap4.min.css') }}">
</head>

<body class="layout-light side-menu">
    <div class="mobile-search">
        <form action="#" class="search-form">
            <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg">
            <input class="form-control me-sm-2 box-shadow-none" type="search" placeholder="Search..."
                aria-label="Search">
        </form>
    </div>
    <div class="mobile-author-actions"></div>
    <header class="header-top">
        <nav class="navbar navbar-light">
            <div class="navbar-left">
                <div class="logo-area">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        @php
                            $siteLogo = getSetting('site_logo');
                            $siteName = getSetting('site_name');
                            $siteLogoUrl = $siteLogo ? asset('storage/' . $siteLogo) : null;
                        @endphp

                        @if ($siteLogoUrl && file_exists(public_path('storage/' . $siteLogo)))
                            <img class="dark" src="{{ $siteLogoUrl }}" alt="{{ $siteName }} Logo">
                        @else
                            <span class="navbar-brand-name"><b>{{ $siteName }}</b></span>
                        @endif

                        <img class="light"
                            src="{{ $siteLogoUrl && file_exists(public_path('storage/' . $siteLogo)) ? $siteLogoUrl : asset('assets/img/logo.png') }}"
                            alt="{{ $siteName }}">
                    </a>
                    <a href="#" class="sidebar-toggle">
                        <img class="svg" src="{{ asset('assets/img/svg/align-center-alt.svg') }}"
                            alt="img"></a>
                </div>
                {{-- <a href="#" class="customizer-trigger">
                    <i class="uil uil-edit-alt"></i>
                    <span>Customize...</span>
                </a> --}}
                {{-- @include('layouts.top-menu') --}}
            </div>

            @include('layouts.top-right-menu')

        </nav>
    </header>
    <main class="main-content">
        @include('layouts.left-sidebar')

        @yield('content')

        @include('layouts.footer')

    </main>
    <div id="overlayer">
        <div class="loader-overlay">
            <div class="dm-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </div>
    </div>
    <div class="overlay-dark"></div>
    <div class="overlay-dark-l2"></div>

    {{-- @include('layouts.customizer') --}}

    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script src="{{ asset('assets/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>

    @php
        $currency = getSetting('currency_symbol', 'TSh');
    @endphp

    <script>
        $(document).ready(function() {
            $('.amount').inputmask({
                alias: 'numeric',
                groupSeparator: ',',
                autoGroup: true,
                digits: 2,
                rightAlign: true,
                prefix: '{{ $currency }} ', // Enclosed in quotes and with a trailing space
                placeholder: '0'
            });

            var defaultPageLength = {{ getSetting('default_page_length', 10) }};
            $('.dataTable').DataTable({
                // Optional: Add your custom configuration options here
                "paging": true, // Enable/disable pagination
                "searching": true, // Enable/disable the search box
                "ordering": true, // Enable/disable column sorting
                "info": true, // Enable/disable table information (like "Showing 1 to 10 of 57 entries")
                "autoWidth": false, // Disable auto-width feature if necessary
                "pageLength": defaultPageLength,
            });
        });

        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd M, yyyy', // Format as "15 Oct, 2024"
                autoclose: true, // Close datepicker after selecting the date
                todayHighlight: true // Highlight today's date
            });

            // Set the default date to today
            $('.datepicker').datepicker('setDate', new Date());
        });
    </script>

    {{-- Notifications functionality removed for standalone Money Point project --}}
    <script>
        // Notifications feature not available in standalone Money Point project
        $(document).ready(function() {
            // Hide notification badge if it exists
            $('#totalPendingBadge, #totalPendingBadgeTitle').hide();
            
            // Show "No notifications" message in notification dropdown
            $('ul.notification-list').html(`
                <li class="nav-notification__single d-flex flex-wrap">
                    <div class="nav-notification__details">
                        <p class="text-center">No notifications</p>
                    </div>
                </li>
            `);
        });
    </script>



    @stack('page_scripts')
</body>

</html>
