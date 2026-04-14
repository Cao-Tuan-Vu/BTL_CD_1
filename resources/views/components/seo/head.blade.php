@php
    $appName = trim((string) config('app.name', 'HomeSpace'));

    $rawTitle = trim((string) $__env->yieldContent('title', $appName));
    $pageTitle = $rawTitle === '' ? $appName : $rawTitle;

    $defaultDescription = trim((string) config('app.seo.default_description', $appName));
    $rawDescription = trim((string) $__env->yieldContent('meta_description', $defaultDescription));
    $metaDescription = $rawDescription === '' ? $defaultDescription : $rawDescription;

    $faviconPath = trim((string) config('app.seo.favicon_path', 'images/logoHomeSpace.png'));
    $faviconUrl = asset(ltrim($faviconPath, '/'));
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="icon" type="image/png" href="{{ $faviconUrl }}">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($__env->hasSection('meta_robots'))
    <meta name="robots" content="@yield('meta_robots')">
@endif
