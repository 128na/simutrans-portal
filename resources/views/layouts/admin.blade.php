<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <link href="{{ asset(mix('css/admin.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('js/manifest.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/admin.js')) }}" defer></script>
</head>

<body>
    @if (session()->has('status'))
        <div class="alert alert-success m-4">{{ session('status') }}</div>
    @endif
    @if (session()->has('success'))
        <div class="alert alert-success m-4">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger m-4">{{ session('error') }}</div>
    @endif

    @yield('content')
</body>

</html>
