<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-seo.head />
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/auth.css') }}">
    @stack('styles')
</head>
<body class="admin-auth-app">
<x-ui.toast />
@yield('content')
<script src="{{ asset('js/components/toast.js') }}" defer></script>
@stack('scripts')
</body>
</html>
