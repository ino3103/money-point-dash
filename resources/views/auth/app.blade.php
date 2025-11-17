<!doctype html>
<html lang="en" dir="ltr">
@php
    $siteName = getSetting('site_name', env('APP_NAME'));
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $siteName }} | AUTH</title>
    <link href="{{ asset('fonts.googleapis.com/css2fb67.css?family=Inter:wght@400;500;600;700&amp;display=swap') }}"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('cdn.jsdelivr.net/npm/%40iconscout/unicons%404.0.8/css/line.min.css') }}">

    @laravelPWA
</head>

<body>
    <main class="main-content">
        <div class="admin">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-8">
                        <div class="edit-profile">
                            <div class="edit-profile__logos">
                                @php
                                    $siteLogo = getSetting('site_logo');
                                    $siteName = getSetting('site_name');
                                    $siteLogoPath = 'storage/' . $siteLogo;
                                    $siteLogoUrl =
                                        $siteLogo && file_exists(public_path($siteLogoPath))
                                            ? asset($siteLogoPath)
                                            : null;
                                @endphp

                                <a href="{{ route('home') }}">
                                    @if ($siteLogoUrl)
                                        <img class="dark" src="{{ $siteLogoUrl }}" alt="{{ $siteName }} Logo">
                                        <img class="light" src="{{ $siteLogoUrl }}" alt="{{ $siteName }} Logo">
                                    @else
                                        <span class="navbar-brand-name">{{ $siteName }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="card border-0">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <div class="enable-dark-mode dark-trigger">
        <ul>
            <li>
                <a href="#">
                    <i class="uil uil-moon"></i>
                </a>
            </li>
        </ul>
    </div>

    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".toggle-password3").click(function() {
                // Toggle the class for the eye icon
                $(this).toggleClass("uil-eye uil-eye-slash");

                // Find the input field related to this button
                let input = $(this).siblings("#password_confirmation");

                // Toggle the input type between password and text
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        });
    </script>

</body>

</html>
