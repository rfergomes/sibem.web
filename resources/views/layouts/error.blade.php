<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>@yield('title') | SIBEM Web</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    @include('partials.pwa-meta')
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="SIBEM CCB" />
    <link rel="manifest" href="{{ asset('assets/manifest.json') }}" />
    
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ccb-theme.css') }}">
    
    @yield('styles')
</head>
<body data-pc-preset="preset-10" data-pc-sidebar-theme="dark" data-pc-direction="ltr" data-pc-theme="light">
    <!-- Preloader -->
    <div class="loader-bg">
        <div class="pc-loader">
            <div class="loader-fill"></div>
        </div>
    </div>

    @yield('content')

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script>
        layout_change('light');
        preset_change("preset-10");
    </script>
    @yield('scripts')
</body>
</html>
