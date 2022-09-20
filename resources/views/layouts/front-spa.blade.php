<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless(\App::environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <link href="{{ asset(mix('css/front.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('js/manifest.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/front_spa.js')) }}" defer></script>
</head>

<body>
    @yield('content')
</body>

</html>
