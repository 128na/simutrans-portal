<nav aria-label="マイページメニュー" class="v2-nav">
  <div class="v2-nav-group">
    <x-ui.nav-link :url="route('mypage.index')" :title="'マイページ'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">記事</p>
    <x-ui.nav-link :url="route('mypage.articles.index')" :title="'記事一覧'" />
    <x-ui.nav-link :url="route('mypage.articles.create')" :title="'記事作成'" />
    <x-ui.nav-link :url="route('mypage.mylists.index')" :title="'マイリスト'" />
    <x-ui.nav-link :url="route('mypage.redirects')" :title="'記事のリダイレクト設定'" />
    <x-ui.nav-link :url="route('mypage.attachments')" :title="'ファイル管理'" />
    <x-ui.nav-link :url="route('mypage.analytics')" :title="'アナリティクス'" />
    <x-ui.nav-link :url="route('mypage.tags')" :title="'タグの編集'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">ユーザー</p>
    <x-ui.nav-link :url="route('mypage.profile')" :title="'プロフィール'" />
    <x-ui.nav-link :url="route('mypage.two-factor')" :title="'二要素認証'" />
    <x-ui.nav-link :url="route('mypage.invite')" :title="'ユーザー招待'" />
    <x-ui.nav-link :url="route('mypage.tokens.index')" :title="'APIトークン'" />
    <x-ui.nav-link :url="route('mypage.login-histories')" :title="'ログイン履歴'" />
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading">その他</p>
    <x-ui.nav-link :url="route('index')" :title="'トップ'" />
    <x-ui.nav-link :url="route('latest')" :title="'新着アドオン'" />
    {{-- 運営費支援ページ(route('support'))。下のsupport_site_urlはサイトの使い方ガイドで別物 --}}
    <x-ui.nav-link :url="route('support')" :title="'サイトを応援する'" />
    <x-ui.nav-link :url="config('app.support_site_url')" :title="'サイトの使い方'" />
  </div>
</nav>
