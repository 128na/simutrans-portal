<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('parts.gtm-header')

    <title>@yield('title') - {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <link href="{{ asset(mix('css/mypage.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('js/manifest.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/mypage.js')) }}" defer></script>
</head>

<body>
    @include('parts.gtm-body')
    @yield('content')
</body>

</html>
