<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $meta['title'] ?? config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <meta name="description" content="{{ $meta['description'] ?? config('app.meta-description') }}">

    <meta name="twitter:card" content="{{ $meta['card_type'] ?? 'summary_large_image' }}">
    <meta name="twitter:site" content="{{ '@' . config('app.twitter') }}">
    <meta name="twitter:creator" content="{{ '@' . config('app.creator') }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset(config('app.meta-image')) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['title'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta['description'] ?? config('app.meta-description') }}">
    <meta property="og:url" content="{{ $meta['canonical'] ?? url()->current() }}">


    {{-- 本番環境以外はnoindex nofollow --}}
    @unless(\App::environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    <link href="{{ asset(mix('css/front.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('js/manifest.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/front_spa.js')) }}" defer></script>
</head>

<body>
    @yield('content')
</body>

</html>
