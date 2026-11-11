@props(['variant' => 'desktop'])

<nav aria-label="サイトメニュー" class="v2-nav">
  {{-- PC 幅の検索欄はヘッダーにあるので、検索欄はモバイルメニューにだけ置く --}}
  @if ($variant === 'mobile')
    <x-layout.search-form input-id="sidebar-search-mobile" />
  @endif
  <div class="v2-nav-group">
    <ul class="v2-nav-group">
      <li><x-ui.nav-link :url="route('latest')" :title="'新着アドオン'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">pak別一覧</p>
    <ul class="v2-nav-group" aria-label="pak別一覧">
      <li><x-ui.nav-link :url="route('pak.128japan')" :title="'pak128.Japan'" /></li>
      <li><x-ui.nav-link :url="route('pak.128')" :title="'pak128'" /></li>
      <li><x-ui.nav-link :url="route('pak.64')" :title="'pak64'" /></li>
      <li><x-ui.nav-link :url="route('pak.others')" :title="'その他のpak'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">ジャンル別一覧</p>
    <ul class="v2-nav-group" aria-label="ジャンル別一覧">
      <li><x-ui.nav-link :url="route('users.index')" :title="'ユーザー'" /></li>
      <li><x-ui.nav-link :url="route('categories.index')" :title="'カテゴリ'" /></li>
      <li><x-ui.nav-link :url="route('tags.index')" :title="'タグ'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">その他</p>
    <ul class="v2-nav-group" aria-label="その他">
      <li><x-ui.nav-link :url="route('pages')" :title="'一般記事'" /></li>
      <li><x-ui.nav-link :url="route('announces')" :title="'お知らせ'" /></li>
      <li><x-ui.nav-link :url="route('search')" :title="'詳細検索'" /></li>
      <li><x-ui.nav-link :url="route('public-mylist.index')" :title="'公開マイリスト一覧'" /></li>
      <li><x-ui.nav-link :url="route('social')" :title="'各種ツール（SNS, RSS, API, MCP）'" /></li>
      {{-- 運営費支援ページ(route('support'))。下のsupport_site_urlはサイトの使い方ガイドで別物 --}}
      <li><x-ui.nav-link :url="route('support')" :title="'サイトを応援する'" /></li>
      <li><x-ui.nav-link :url="config('app.support_site_url')" :title="'サイトの使い方'" /></li>
      <li><x-ui.nav-link :url="config('app.privacy_policy_url')" :title="'プライバシーポリシー'" /></li>
    </ul>
  </div>
</nav>
