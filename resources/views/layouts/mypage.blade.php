<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <script defer src="{{ asset(mix('/js/vendor.js')) }}"></script>
    <script defer src="{{ asset(mix('/js/app.js')) }}"></script>
    <link href="{{ asset(mix('/css/vendor.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix('/css/app.css')) }}" rel="stylesheet">
</head>

<body>
    <div id=q-app></div>
</body>

</html>
