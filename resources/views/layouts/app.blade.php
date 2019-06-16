<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
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
        footer {
            /* padding: 0.5rem 1rem; */
        }
        .list .img-thumbnail {
            max-height: 128px;
            max-width: 100%;
        }
        .list .article-box {
            display:flex;

        }
        .detail .img-thumbnail {
            max-height: 512px;
            max-width: 100%;
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
                <ul class="navbar-nav mr-auto">
                    @foreach ($categories as $category)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="{{ $category->slug }}-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $category->name }}</a>
                            <div class="dropdown-menu" aria-labelledby="{{ $category->slug }}-dropdown">
                                @foreach ($category->children as $child)
                                    <a class="dropdown-item" href="#">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('mypage.index') }}">{{ __('MyPage') }}</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                            >{{ __('Logout') }}</a>
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
        @yield('content')
    </main>
    <footer class="navbar-dark bg-primary">
        <a class="navbar-brand" href="{{ route('index') }}">created by 128na</a>
    </footer>


    </div>
</body>
</html>
