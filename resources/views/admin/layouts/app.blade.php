<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-seo.head />
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/shared.css') }}">
    @stack('styles')
</head>
<body class="admin-app">
<x-ui.toast />
<div class="admin-shell">
    @include('admin.layouts.partials.sidebar')

    <main class="admin-main">
        <div class="container">
            @yield('content')
        </div>
    </main>
</div>

<script src="{{ asset('js/components/toast.js') }}" defer></script>
@stack('scripts')
</body>
</html>
