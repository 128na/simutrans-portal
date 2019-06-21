<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height:100vh;
        }
        body>main {
            flex:1;
        }
        .list .img-thumbnail,
        .form-group .img-thumbnail {
            max-height: 128px;
            max-width: 256px;
        }
        .list .article-box {
            display:flex;
        }
        .detail .img-thumbnail {
            max-height: 512px;
            max-width: 100%;
        }
        .badge {
            padding: 4px;
        }
        .category-list {
            display: flex;
            flex-wrap: wrap;
        }
        .category-list .custom-checkbox,
        .category-list .custom-radio {
            min-width: 11rem;
        }
        @media (max-width: 768px) {
            .list .article-box {
                flex-direction: column;
            }
            .list .img-thumbnail {
                width: : 100%;
            }
        }

    </style>
</head>
<body>
    <header class="global-menu">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="{{ route('index') }}">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#global-menu" aria-controls="global-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="global-menu">
                <ul class="navbar-nav ml-auto mr-2">
                    @if (isset($menu_pak_addon_counts))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Addons
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                @foreach ($menu_pak_addon_counts as $pak_name => $addons)
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">{{ $pak_name }}</a>
                                        <ul class="dropdown-menu">
                                            @foreach ($addons as $addon)
                                                <li><a class="dropdown-item" href="{{ route('category.pak.addon', [
                                                        $addon->pak_slug, $addon->addon_slug]) }}">{{ $addon->addon_name }} <small>( {{ $addon->count }} )</small></a></li>
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
                                Users
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
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @else

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="mypage-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                            <div class="dropdown-menu" aria-labelledby="mypage-dropdown">
                                <a class="dropdown-item" href="{{ route('mypage.index') }}">{{ __('MyPage') }}</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                            </div>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
                </form>
            </div>
        </nav>
    </header>
    <main class="container bg-light py-4">
        <h1>@yield('title')</h1>

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
    <footer class="navbar-dark bg-primary">
        <a class="navbar-brand" href="{{ route('index') }}">created by 128na</a>
    </footer>


    </div>
</body>
</html>
