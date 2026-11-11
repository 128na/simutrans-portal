@props(['variant' => 'desktop'])

<nav aria-label="サイトメニュー" class="v2-nav">
  <form method="GET" action="{{ route('search') }}">
    <div class="flex items-center gap-x-2 w-full">
      <label for="sidebar-search-{{ $variant }}" class="sr-only">キーワード</label>
      <input
        id="sidebar-search-{{ $variant }}"
        type="search"
        name="word"
        placeholder="キーワードを入力"
        class="flex-1 min-w-0 v2-input"
      />
      <button type="submit" class="v2-button v2-button-md v2-button-primary flex-shrink-0">検索</button>
    </div>
  </form>
  <div class="v2-nav-group">
    <x-ui.nav-link :url="route('latest')" :title="'新着アドオン'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">pak別一覧</p>
    <x-ui.nav-link :url="route('pak.128japan')" :title="'pak128.Japan'" />
    <x-ui.nav-link :url="route('pak.128')" :title="'pak128'" />
    <x-ui.nav-link :url="route('pak.64')" :title="'pak64'" />
    <x-ui.nav-link :url="route('pak.others')" :title="'その他のpak'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">ジャンル別一覧</p>
    <x-ui.nav-link :url="route('users.index')" :title="'ユーザー'" />
    <x-ui.nav-link :url="route('categories.index')" :title="'カテゴリ'" />
    <x-ui.nav-link :url="route('tags.index')" :title="'タグ'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">その他</p>
    <x-ui.nav-link :url="route('pages')" :title="'一般記事'" />
    <x-ui.nav-link :url="route('announces')" :title="'お知らせ'" />
    <x-ui.nav-link :url="route('search')" :title="'詳細検索'" />
    <x-ui.nav-link :url="route('public-mylist.index')" :title="'公開マイリスト一覧'" />
    <x-ui.nav-link :url="route('social')" :title="'各種ツール（SNS, RSS, API, MCP）'" />
    {{-- 運営費支援ページ(route('support'))。下のsupport_site_urlはサイトの使い方ガイドで別物 --}}
    <x-ui.nav-link :url="route('support')" :title="'サイトを応援する'" />
    <x-ui.nav-link :url="config('app.support_site_url')" :title="'サイトの使い方'" />
    <x-ui.nav-link :url="config('app.privacy_policy_url')" :title="'プライバシーポリシー'" />
  </div>
</nav>
