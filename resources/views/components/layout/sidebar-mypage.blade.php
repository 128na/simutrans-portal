<nav aria-label="Sidebar" class="space-y-4">
  <ul class="space-y-4">
    <li>
      @include('components.ui.link', ['url' => route('mypage.index'), 'title' => 'マイページ'])
    </li>
    <li>
      記事
      <ul class="ml-3 space-y-2">
        <li>
          @include('components.ui.link', ['url' => route('mypage.articles.index'), 'title' => '記事一覧'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.articles.create'), 'title' => '記事作成'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.mylists.index'), 'title' => 'マイリスト'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.redirects'), 'title' => '記事のリダイレクト設定'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.attachments'), 'title' => 'ファイル管理'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.analytics'), 'title' => 'アナリティクス'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.tags'), 'title' => 'タグの編集'])
        </li>
      </ul>
    </li>
    <li>
      ユーザー
      <ul class="ml-3 space-y-2">
        <li>
          @include('components.ui.link', ['url' => route('mypage.profile'), 'title' => 'プロフィール'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.two-factor'), 'title' => '二要素認証'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.invite'), 'title' => 'ユーザー招待'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('mypage.login-histories'), 'title' => 'ログイン履歴'])
        </li>
      </ul>
    </li>
    <li>
      その他
      <ul class="ml-3 space-y-2">
        <li>
          @include('components.ui.link', ['url' => route('index'), 'title' => 'トップ'])
        </li>
        <li>
          @include('components.ui.link', ['url' => route('latest'), 'title' => '新着アドオン'])
        </li>
        <li>
          @include('components.ui.link', ['url' => config('app.support_site_url'), 'title' => 'サイトの使い方'])
        </li>
      </ul>
    </li>
  </ul>
</nav>
