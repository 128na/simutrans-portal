<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ isset($preview) ? __('[Preview]') : ''}}@yield('title') - {{ config('app.name') }}</title>

        @includeWhen(\App::environment('production'), 'parts._ga')

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="api-entrypoint" content="{{ config('app.url') }}">

        <meta name="description" content="@yield('meta-description')">

        <meta property="og:type"        content="website">
        <meta property="og:title"       content="@yield('title') - {{ config('app.name') }}">
        <meta property="og:description" content="@yield('meta-description')">
        <meta property="og:url"         content="{{ $canonical_url ?? url()->current() }}">

        <meta name="twitter:card"    content="@yield('card-type', 'summary_large_image')">
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
        <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-left py-4">
            @include('parts.menu')
        </nav>

        <main id="@yield('id')" class="container-fluid bg-light py-4">
            @if (session()->has('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (isset($preview))
                <div class="alert alert-warning">@lang('This is a preview display. Articles have not been saved or updated.')</div>
            @endif

            @includeWhen(!empty($breadcrumb), 'parts.breadcrumb')

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
    </body>
</html>
