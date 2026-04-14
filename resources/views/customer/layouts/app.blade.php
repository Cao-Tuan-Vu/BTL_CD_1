<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-seo.head />
    @php
        $versionedAsset = static function (string $path): string {
            $version = @filemtime(public_path($path));

            return asset($path) . ($version ? ('?v=' . $version) : '');
        };
    @endphp
    <link rel="stylesheet" href="{{ $versionedAsset('css/global.css') }}">
    <link rel="stylesheet" href="{{ $versionedAsset('css/components/toast.css') }}">
    <link rel="stylesheet" href="{{ $versionedAsset('css/customer/layout.css') }}">
    <link rel="stylesheet" href="{{ $versionedAsset('css/customer/shared.css') }}">
    <link rel="stylesheet" href="{{ $versionedAsset('css/chat-widget.css') }}">
    @stack('styles')
</head>
<body class="customer-app">
@php
    $customerCartCount = collect(session('customer_cart', []))->sum();
    $currentUser = auth()->user();
    $isCustomerAuthenticated = $currentUser && $currentUser->role === 'customer';
@endphp

@include('customer.layouts.partials.header', [
    'customerCartCount' => $customerCartCount,
    'currentUser' => $currentUser,
    'isCustomerAuthenticated' => $isCustomerAuthenticated,
])

<x-ui.toast />

<main>
    <div class="container">
        @yield('content')
    </div>
</main>

@include('customer.layouts.partials.footer')
@include('customer.layouts.partials.chat-widget')

<script src="{{ $versionedAsset('js/components/toast.js') }}" defer></script>
<script src="{{ $versionedAsset('js/chat-widget.js') }}" defer></script>
<script src="{{ $versionedAsset('js/ui-effects.js') }}" defer></script>

@stack('scripts')
</body>
</html>
