@props(['variant' => 'desktop'])

@php
    $isMobile = $variant === 'mobile';
    $linkClass = $isMobile
        ? 'v2-header-menu-item-sp flex items-center gap-2'
        : 'flex items-center gap-2 text-sm/6 font-semibold text-c-main hover:text-c-main/70';
    $searchWrapperClass = $isMobile ? 'flex items-center gap-x-2 w-full' : 'flex items-center gap-x-2 w-full';
    $searchInputClass = $isMobile ? 'flex-1 min-w-0 v2-input' : 'flex-1 min-w-0 v2-input';
    $searchButtonClass = $isMobile
        ? 'v2-button v2-button-md v2-button-primary flex-shrink-0'
        : 'v2-button v2-button-md v2-button-primary flex-shrink-0';
@endphp

<nav aria-label="Sidebar" class="space-y-4">
    <form method="GET" action="{{ route('search') }}">
        <div class="{{ $searchWrapperClass }}">
            <input type="search" name="word" placeholder="キーワードを入力" class="{{ $searchInputClass }}" />
            <button type="submit" class="{{ $searchButtonClass }}">検索</button>
        </div>
    </form>
    <ul class="space-y-4">
        <li>@include('components.ui.link', ['url' => route('latest'), 'title' => '新着アドオン'])</li>
        <li>
        pak別一覧
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('pak.128japan'), 'title' => 'pak128.Japan'])</li>
            <li>@include('components.ui.link', ['url' => route('pak.128'), 'title' => 'pak128'])</li>
            <li>@include('components.ui.link', ['url' => route('pak.64'), 'title' => 'pak64'])</li>
            <li>@include('components.ui.link', ['url' => route('pak.others'), 'title' => 'その他のpak'])</li>
        </ul>
        </li>
        <li>
        ジャンル別一覧
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('users.index'), 'title' => 'ユーザー'])</li>
            <li>@include('components.ui.link', ['url' => route('categories.index'), 'title' => 'カテゴリ'])</li>
            <li>@include('components.ui.link', ['url' => route('tags.index'), 'title' => 'タグ'])</li>
        </ul>
        </li>
        <li>
        その他
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('pages'), 'title' => '一般記事'])</li>
            <li>@include('components.ui.link', ['url' => route('announces'), 'title' => 'お知らせ'])</li>
            <li>@include('components.ui.link', ['url' => route('search'), 'title' => '詳細検索'])</li>
            <li>@include('components.ui.link', ['url' => route('social'), 'title' => '各種ツール（SNS, RSS, API, MCP）'])</li>
            <li>@include('components.ui.link', ['url' => config('app.support_site_url'), 'title' => 'サイトの使い方'])</li>
            <li>@include('components.ui.link', ['url' => config('app.privacy_policy_url'), 'title' => 'プライバシーポリシー'])</li>
        </ul>
        </li>
    </ul>
</nav>
