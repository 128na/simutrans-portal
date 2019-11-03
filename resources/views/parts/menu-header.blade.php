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

                {{-- 言語一覧 --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLangs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('__Current_Language__')
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownLangs">
                        @foreach (config('languages', []) as $name => $language)
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ route('language', $name) }}">{{ $language['label'] }}</a></li>
                        @endforeach
                    </ul>
                </li>

                @if (isset($menu_pak_addon_counts))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAddons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @lang('By pak')
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownAddons">
                            @foreach ($menu_pak_addon_counts as $pak_slug => $addons)
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">@lang('category.pak.'.$pak_slug)</a>
                                    <ul class="dropdown-menu">
                                        @foreach ($addons as $addon)
                                            <li><a class="dropdown-item" href="{{ route('category.pak.addon', [
                                                    $addon->pak_slug, $addon->addon_slug]) }}">@lang('category.addon.'.$addon->addon_slug) <small>( {{ $addon->count }} )</small></a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @if (isset($menu_user_addon_counts))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsers" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @lang('By user')
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
                            @foreach ($menu_user_addon_counts as $user_addon_count)
                                <li><a class="dropdown-item" href="{{ route('user', [$user_addon_count->user_id]) }}">
                                    {{ $user_addon_count->user_name }} <small>( {{ $user_addon_count->count }} )</small></a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">@lang('Login')</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">@lang('Register')</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="mypage-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}</a>
                        <div class="dropdown-menu {{ $show_searchbox ?: 'dropdown-menu-right' }}" aria-labelledby="mypage-dropdown">
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item" href="{{ route('admin.index') }}">@lang('[admin] Dashboard')</a>
                                <a class="dropdown-item" href="{{ route('admin.users.index') }}">@lang('[admin] User list')</a>
                                <a class="dropdown-item" href="{{ route('admin.articles.index') }}">@lang('[admin] Article list')</a>
                                <div class="dropdown-divider my-1"></div>
                            @endif
                                <a class="dropdown-item" href="{{ route('mypage.index') }}">@lang('Mypage')</a>
                                <a class="dropdown-item" href="{{ route('mypage.articles.create', 'addon-post') }}">
                                    @lang('Create :post_type', ['post_type' => __('post_types.addon-post')])</a>
                                <a class="dropdown-item" href="{{ route('mypage.articles.create', 'addon-introduction') }}">
                                    @lang('Create :post_type', ['post_type' => __('post_types.addon-introduction')])</a>
                                <a class="dropdown-item" href="{{ route('mypage.articles.create', 'page') }}">
                                    @lang('Create :post_type', ['post_type' => __('post_types.page')])</a>
                                <a class="dropdown-item" href="{{ route('mypage.articles.create', 'markdown') }}">
                                    @lang('Create :post_type', ['post_type' => __('post_types.markdown')])</a>
                                <div class="dropdown-divider my-1"></div>

                                <a class="dropdown-item" href="{{ route('mypage.profile.edit') }}">@lang('Edit my profile')</a>
                                <div class="dropdown-divider my-1"></div>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">@lang('Logout')</a>
                        </div>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </ul>
            @if ($show_searchbox)
                <form class="form-inline my-2 my-lg-0" action="{{ route('search') }}" method="GET">
                    <input class="form-control mr-sm-2" name="s" type="search" placeholder="@lang('Search words')" aria-label="Search">
                    <button class="btn btn-outline-light my-2 my-sm-0" type="submit">@lang('Search')</button>
                </form>
            @endif
        </div>
    </nav>
    @if (isset($menu_pak_addon_counts))
        @php
            $schemas = [];
            foreach ($menu_pak_addon_counts as $pak_slug => $addons) {
                foreach ($addons as $addon) {
                    $schemas[] = [
                        '@context'=> 'http://schema.org',
                        '@type'=> 'SiteNavigationElement',
                        'name'=> __('category.pak.'.$pak_slug).'/'.__('category.addon.'.$addon->addon_slug),
                        'url'=> route('category.pak.addon', [$addon->pak_slug, $addon->addon_slug]),
                    ];
                }
            }
        @endphp
        <script type="application/ld+json">
            @json($schemas)
        </script>
    @endif
</header>
