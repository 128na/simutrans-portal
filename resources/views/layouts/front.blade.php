<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($preview) ? '[プレビュー]' : '' }}@yield('title') - {{ config('app.name') }}</title>

    @includeWhen(\App::environment('production'), 'parts._ga')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-entrypoint" content="{{ config('app.url') }}">

    <meta name="description" content="@yield('meta-description')">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta-description')">
    <meta property="og:url" content="{{ $canonical_url ?? url()->current() }}">

    <meta name="twitter:card" content="@yield('card-type', 'summary_large_image')">
    <meta name="twitter:site" content="{{ '@' . config('app.twitter') }}">
    <meta name="twitter:creator" content="{{ '@' . config('app.creator') }}">
    <meta name="twitter:image" content="@yield('meta-image')">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless(\App::environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link href="{{ asset(mix('css/front.css')) }}" rel="stylesheet">
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <script src="{{ asset(mix('js/front.js')) }}" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-left py-2 py-lg-4">
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
            <div class="alert alert-warning">プレビュー表示です。記事は保存・更新されていません。</div>
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

        @auth
            <div class="modal fade" id="add-bookmark" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><span id="from-item-name"></span>をブックマークに追加</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="add-bookmark-form" action="{{ route('bookmarkItems.store') }}" method="POST">
                                @csrf
                                <input type="hidden" id="from-bookark-item-type"
                                    name="bookmarkItem[bookmark_itemable_type]">
                                <input type="hidden" id="from-bookark-item-id" name="bookmarkItem[bookmark_itemable_id]">
                                <div class="form-group">
                                    <label for="form-bookmark">追加するブックマーク</label>
                                    <select class="form-control" id="form-bookmark" name="bookmarkItem[bookmark_id]">
                                        @foreach (Auth::user()->bookmarks as $bookmark)
                                            <option value="{{ $bookmark->id }}">{{ $bookmark->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="form-memo">メモ</label>
                                    <textarea class="form-control" id="form-memo" name="bookmarkItem[memo]"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-outline-primary" type="submit">{{ $message ?? '追加' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </main>
</body>

</html>
