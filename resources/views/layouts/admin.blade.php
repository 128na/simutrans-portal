<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <script defer src="{{ asset('/js/vendor.js') }}"></script>
    <script defer src="{{ asset('/js/app.js') }}"></script>
    <link href="{{ asset('/css/vendor.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id=q-app></div>
</body>

</html>
