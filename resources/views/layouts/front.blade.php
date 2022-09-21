<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless(\App::environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link href="{{ asset(mix('css/front.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('js/manifest.js')) }}" defer></script>
    <script src="{{ asset(mix('js/vendor.js')) }}" defer></script>
    <script src="{{ asset(mix('js/front.js')) }}" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-left py-2 py-lg-4">
        @include('parts.menu')
    </nav>

    <main id="@yield('id')" class="container-fluid py-4">
        @if (session()->has('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
