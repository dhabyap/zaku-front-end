<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Zaku') }}</title>
    <script>
        // Inline execution to prevent white flash
        (function() {
            try {
                if (localStorage.getItem('zaku_theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch(e) {}
        })();
    </script>
    <link rel="icon" type="image/svg+xml" href="{{ asset('zaku-favicon.svg') }}">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#111010">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ZAKU">
    <link rel="apple-touch-icon" href="{{ asset('zaku-favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
</head>
<body>
    <div class="shell" id="app-shell">
        @include('components.toast-notification')
        @include('components.confirm-modal')
        @include('components.desktop-navigation')

        <div class="app-content" id="app-content">
            @yield('content')
        </div>

        @include('components.navigation')
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
