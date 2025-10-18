<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['title'] ?? config('app.name') }}</title>

    @production
    @include('ga')
    @endproduction

    <meta name="twitter:card" content="{{ $meta['card_type'] ?? 'summary_large_image' }}">
    <meta name="twitter:site" content="{{ '@' . config('app.twitter') }}">
    <meta name="twitter:creator" content="{{ '@' . config('app.creator') }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset(config('app.meta-image')) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['title'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta['description'] ?? config('app.meta-description') }}">
    <meta property="og:url" content="{{ $meta['canonical'] ?? url()->current() }}">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless (\Illuminate\Support\Facades\App::environment('production'))
    <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    <link rel=icon type=image/ico href=/favicon.ico>
    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.google_recaptcha.siteKey') }}">
    </script>
    @include('onesignal')

</head>

<body>
    @include('v2.parts.header')
    @yield('content')
</body>

</html>
