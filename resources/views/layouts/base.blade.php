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

<body class="text-c-main bg-white">
    @yield('header')
    @include('components.ui.session-message')
    @yield('content')
    <script id="data-is-authenticated" type="application/json">{{ Auth::check() ? 'true' : 'false' }}</script>
    @stack('scripts')
</body>

</html>
