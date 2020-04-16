<a class="navbar-brand p-0 mb-md-4 mb-0" href="{{ route('index') }}">{{ config('app.name', 'Laravel') }}</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#global-menu" aria-controls="global-menu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="global-menu">
    <form class="form-inline mb-2" action="{{ route('search') }}" method="GET">
        <div class="input-group">
            <input class="form-control" name="word" type="search" placeholder="@lang('Search words')" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-outline-light" type="submit">@lang('Search')</button>
            </div>
        </div>
    </form>
    <ul class="navbar-nav ml-auto mr-2">
        {{-- Pak別カテゴリ一覧 --}}
        @if (isset($menu_pak_addon_counts))
            @foreach ($menu_pak_addon_counts as $pak_slug => $addons)
                <li class="nav-item">
                    <a class="nav-link active collapsed with-icon" data-toggle="collapse" href="#collapse-{{$pak_slug}}" aria-expanded="false" aria-controls="collapse-{{$pak_slug}}">
                        @lang('category.pak.'.$pak_slug)
                    </a>
                    <ul class="navbar-nav ml-3 collapse" id="collapse-{{$pak_slug}}">
                        @foreach ($addons as $addon)
                            <li class="nav-item">
                                <a class="nav-link active py-1" href="{{ route('category.pak.addon', [$addon->pak_slug, $addon->addon_slug]) }}">
                                    @lang('category.addon.'.$addon->addon_slug) <small>( {{ $addon->count }} )</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        @endif
        {{-- ユーザー一覧 --}}
        @if (isset($menu_user_addon_counts))
            <li class="nav-item">
                <a class="nav-link active collapsed with-icon" data-toggle="collapse" href="#collapse-user" aria-expanded="false" aria-controls="collapse-user">
                    @lang('By user')
                </a>
                <ul class="navbar-nav ml-3 collapse" id="collapse-user">
                    @foreach ($menu_user_addon_counts as $user_addon_count)
                        <li class="nav-item">
                            <a class="nav-link active py-1" href="{{ route('user', [$user_addon_count->user_id]) }}">
                                {{ $user_addon_count->user_name }} <small>( {{ $user_addon_count->count }} )</small>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif
        {{-- 言語一覧 --}}
        <li class="nav-item">
            <a class="nav-link active collapsed with-icon" data-toggle="collapse" href="#collapse-lang" aria-expanded="false" aria-controls="collapse-lang">
                @lang('__Current_Language__')
            </a>
            <ul class="navbar-nav ml-3 collapse" id="collapse-lang">
                @foreach (config('languages', []) as $name => $language)
                    <li class="nav-item">
                        <a class="nav-link active py-1" href="{{ route('language', $name) }}">{{ $language['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        <div class="dropdown-divider border-light"></div>
        {{-- ログイン・登録/マイページ --}}
        @guest
            <li class="nav-item"><a class="nav-link active" href="{{ route('mypage.index') }}">@lang('Login / Register')</a></li>
        @else
            @if(Auth::user()->isAdmin())
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.index') }}">@lang('[admin] Dashboard')</a></li>
            @endif
            <li class="nav-item"><a class="nav-link active" href="{{ route('mypage.index') }}">@lang('Mypage')</a></li>
            <li class="nav-item"><a class="nav-link active" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">@lang('Logout')</a></li>
            <form id="logout-form" action="{{ route('api.v2.logout') }}" method="POST" style="display: none;">@csrf</form>
        @endguest
        <div class="dropdown-divider border-light"></div>

        @include('parts.copyright')

    </ul>
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
</div>
