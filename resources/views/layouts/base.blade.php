<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['title'] ?? config('app.name') }}</title>

    @production
    @include('components.partials.ga')
    @endproduction

    @include('components.partials.meta-tags')

    @stack('styles')
</head>

<body>
    @yield('header')
    @include('components.ui.session-message')
    @yield('content')
    @stack('scripts')
</body>

</html>
