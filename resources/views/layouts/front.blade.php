<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $meta['title'] ?? config('app.name') }}</title>

    @production
        @include('newrelic')
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
    <script defer src="{{ asset(mix('/js/vendor.js')) }}"></script>
    <script defer src="{{ asset(mix('/js/app.js')) }}"></script>
    <link href="{{ asset(mix('/css/vendor.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix('/css/app.css')) }}" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.google_recaptcha.siteKey') }}">
    </script>
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ config('onesignal.app_id') }}",
            });
        });
    </script>
</head>

<body>
    <div id=q-app></div>
</body>

</html>
