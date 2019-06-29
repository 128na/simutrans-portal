<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($preview) ? __('message.preview-mode') : ''}}@yield('title') - {{ config('app.name') }}</title>

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

    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>
</head>
<body>
    <header class="global-menu">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="{{ route('index') }}">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#global-menu" aria-controls="global-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="global-menu">
                @php
                    $show_searchbox = isset($menu_pak_addon_counts) || isset($menu_user_addon_counts);
                @endphp
                <ul class="navbar-nav ml-auto mr-2">
                    @if (isset($menu_pak_addon_counts))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('message.by-addons') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                @foreach ($menu_pak_addon_counts as $pak_slug => $addons)
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">{{ __('category.pak.'.$pak_slug) }}</a>
                                        <ul class="dropdown-menu">
                                            @foreach ($addons as $addon)
                                                <li><a class="dropdown-item" href="{{ route('category.pak.addon', [
                                                        $addon->pak_slug, $addon->addon_slug]) }}">{{ __('category.addon.'.$addon->addon_slug) }} <small>( {{ $addon->count }} )</small></a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @if (isset($menu_user_addon_counts))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('message.by-users') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                @foreach ($menu_user_addon_counts as $user_addon_count)
                                    <li><a class="dropdown-item" href="{{ route('user', [$user_addon_count->user_id]) }}">
                                        {{ $user_addon_count->user_name }} <small>( {{ $user_addon_count->count }} )</small></a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('message.login') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('message.register') }}</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="mypage-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('message.title-of-user', ['name' => Auth::user()->name]) }}</a>
                            <div class="dropdown-menu {{ $show_searchbox ?: 'dropdown-menu-right' }}" aria-labelledby="mypage-dropdown">
                                @if(Auth::user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('admin.index') }}">{{ __('admin.index') }}</a>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">{{ __('admin.users.index') }}</a>
                                    <a class="dropdown-item" href="{{ route('admin.articles.index') }}">{{ __('admin.articles.index') }}</a>
                                    <div class="dropdown-divider my-1"></div>
                                @endif
                                    <a class="dropdown-item" href="{{ route('mypage.index') }}">{{ __('message.mypage') }}</a>
                                    <a class="dropdown-item" href="{{ route('mypage.articles.create', 'addon-post') }}">
                                        {{ __('message.create-article-of', ['type' => __('category.post.addon-post')]) }}</a>
                                    <a class="dropdown-item" href="{{ route('mypage.articles.create', 'addon-introduction') }}">
                                        {{ __('message.create-article-of', ['type' => __('category.post.addon-introduction')]) }}</a>
                                    <a class="dropdown-item" href="{{ route('mypage.articles.create', 'page') }}">
                                        {{ __('message.create-article-of', ['type' => __('category.post.page')]) }}</a>
                                    <div class="dropdown-divider my-1"></div>

                                    <a class="dropdown-item" href="{{ route('mypage.profile.edit') }}">{{ __('message.edit-profile') }}</a>
                                    <div class="dropdown-divider my-1"></div>

                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('message.logout') }}</a>
                            </div>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </ul>
                @if ($show_searchbox)
                    <form class="form-inline my-2 my-lg-0" action="{{ route('search') }}" method="GET">
                        <input class="form-control mr-sm-2" name="s" type="search" placeholder="{{ __('message.search-word') }}" aria-label="Search">
                        <button class="btn btn-outline-light my-2 my-sm-0" type="submit">{{ __('message.search') }}</button>
                    </form>
                @endif
            </div>
        </nav>
    </header>
    <main class="container bg-white py-2">
        @if (isset($preview))
            <div class="alert alert-warning">{{ __('message.preview-text') }}</div>
        @endif
        @if (session()->has('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (!empty($breadcrumb))
            @include('parts.breadcrumb')
        @endif

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
    <footer class="navbar-dark bg-primary px-2">
        <small><a class="text-white"href="{{ route('index') }}">created by 128na</a></small>
    </footer>
</body>
</html>
