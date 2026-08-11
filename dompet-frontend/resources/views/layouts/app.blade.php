<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Zaku') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('zaku-favicon.svg') }}">
    <script>
        (function() {
            var token = localStorage.getItem('access_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }
            try {
                var payload = JSON.parse(atob(token.split('.')[1]));
                if (payload.exp && Date.now() >= (payload.exp * 1000) + 60000) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('refresh_token');
                    sessionStorage.removeItem('user');
                    document.cookie = 'zaku_token=; path=/; max-age=0';
                    window.location.href = '/login?session=expired';
                }
            } catch(e) {
                // Invalid token — clear and redirect
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                sessionStorage.removeItem('user');
                document.cookie = 'zaku_token=; path=/; max-age=0';
                window.location.href = '/login';
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
</head>
<body>
    <div class="shell" id="app-shell">
        @include('components.toast-notification')
        @include('components.confirm-modal')

        <div style="flex:1;overflow:hidden;position:relative;">
            @yield('content')
        </div>

        @include('components.navigation')
    </div>
</body>
</html>
