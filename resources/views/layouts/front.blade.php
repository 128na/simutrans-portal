<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($preview) ? __('message.preview-mode') : ''}}@yield('title') - {{ config('app.name') }}</title>

    {{-- Google Tag Manager --}}
    @if (\App::environment('production'))
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MLK48JC');</script>
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <meta name="description" content="@yield('meta-description')">

    <meta property="og:type"        content="website">
    <meta property="og:title"       content="@yield('title') - {{ config('app.name') }}">
    <meta property="og:description" content="@yield('meta-description')">
    <meta property="og:url"         content="{{ $canonical_url ?? url()->current() }}">

    <meta name="twitter:card"    content="summary_large_image">
    <meta name="twitter:site"    content="{{ '@'.config('app.twitter') }}">
    <meta name="twitter:creator" content="{{ '@'.config('app.creator') }}">
    <meta name="twitter:image" content="@yield('meta-image')">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless (\App::environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link href="{{ asset(mix('css/front.css')) }}" rel="stylesheet">
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <script src="{{ asset(mix('js/front.js')) }}" defer></script>
</head>
<body>
    {{-- Google Tag Manager --}}
    @if (\App::environment('production'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLK48JC"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @include('parts.menu-header')

    <main class="container bg-white py-2">
        @if (isset($preview))
            <div class="alert alert-warning">{{ __('message.preview-text') }}</div>
        @endif

        @unless (empty($breadcrumb))
            @include('parts.breadcrumb')
        @endunless

        @yield('before_title')

        <h1>@yield('title')</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @include('parts.menu-footer')

</body>
</html>
