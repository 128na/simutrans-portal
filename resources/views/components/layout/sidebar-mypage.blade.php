<nav aria-label="マイページメニュー" class="v2-nav">
  <div class="v2-nav-group">
    <ul class="v2-nav-group">
      <li><x-ui.nav-link :url="route('mypage.index')" :title="'マイページ'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">記事</p>
    <ul class="v2-nav-group" aria-label="記事">
      <li><x-ui.nav-link :url="route('mypage.articles.index')" :title="'記事一覧'" /></li>
      <li><x-ui.nav-link :url="route('mypage.articles.create')" :title="'記事作成'" /></li>
      <li><x-ui.nav-link :url="route('mypage.mylists.index')" :title="'マイリスト'" /></li>
      <li><x-ui.nav-link :url="route('mypage.redirects')" :title="'記事のリダイレクト設定'" /></li>
      <li><x-ui.nav-link :url="route('mypage.attachments')" :title="'ファイル管理'" /></li>
      <li><x-ui.nav-link :url="route('mypage.analytics')" :title="'アナリティクス'" /></li>
      <li><x-ui.nav-link :url="route('mypage.tags')" :title="'タグの編集'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">ユーザー</p>
    <ul class="v2-nav-group" aria-label="ユーザー">
      <li><x-ui.nav-link :url="route('mypage.profile')" :title="'プロフィール'" /></li>
      <li><x-ui.nav-link :url="route('mypage.two-factor')" :title="'二要素認証'" /></li>
      <li><x-ui.nav-link :url="route('mypage.invite')" :title="'ユーザー招待'" /></li>
      <li><x-ui.nav-link :url="route('mypage.tokens.index')" :title="'APIトークン'" /></li>
      <li><x-ui.nav-link :url="route('mypage.login-histories')" :title="'ログイン履歴'" /></li>
    </ul>
  </div>
  <div class="v2-nav-group">
    <p class="v2-nav-heading" aria-hidden="true">その他</p>
    <ul class="v2-nav-group" aria-label="その他">
      <li><x-ui.nav-link :url="route('index')" :title="'トップ'" /></li>
      <li><x-ui.nav-link :url="route('latest')" :title="'新着アドオン'" /></li>
      {{-- 運営費支援ページ(route('support'))。下のsupport_site_urlはサイトの使い方ガイドで別物 --}}
      <li><x-ui.nav-link :url="route('support')" :title="'サイトを応援する'" /></li>
      <li><x-ui.nav-link :url="config('app.support_site_url')" :title="'サイトの使い方'" /></li>
    </ul>
  </div>
</nav>
