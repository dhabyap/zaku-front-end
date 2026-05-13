<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DOMPET') }}</title>
    <script>
        if (!localStorage.getItem('access_token')) {
            window.location.href = '/login';
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
</head>
<body>
    <div class="shell" id="app-shell">
        @include('components.toast-notification')

        <div style="flex:1;overflow:hidden;position:relative;padding-bottom:72px;">
            @yield('content')
        </div>

        @include('components.navigation')
    </div>
</body>
</html>
