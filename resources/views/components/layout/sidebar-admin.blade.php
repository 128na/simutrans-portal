<nav aria-label="Sidebar" class="space-y-4">
  <ul class="space-y-4">
    <li>
      <x-ui.link :url="route('admin.index')" :title="'管理トップ'" />
    </li>
    <li>
      X連携
      <ul class="ml-3 space-y-2">
        <li>
          <x-ui.link :url="route('admin.oauth.twitter.authorize')" :title="'認証'" />
        </li>
        <li>
          <x-ui.link :url="route('admin.oauth.twitter.refresh')" :title="'トークンリフレッシュ'" />
        </li>
        <li>
          <x-ui.link :url="route('admin.oauth.twitter.revoke')" :title="'トークン削除'" />
        </li>
      </ul>
    </li>
    @if (Illuminate\Support\Facades\Route::has('l5-swagger.default.api'))
      <li>
        ドキュメント
        <ul class="ml-3 space-y-2">
          <li>
            <x-ui.link :url="route('l5-swagger.default.api')" :title="'APIドキュメント'" />
          </li>
        </ul>
      </li>
    @endif
    <li>
      その他
      <ul class="ml-3 space-y-2">
        <li>
          <x-ui.link :url="route('index')" :title="'トップページ'" />
        </li>
        <li>
          <x-ui.link :url="route('mypage.index')" :title="'マイページ'" />
        </li>
      </ul>
    </li>
  </ul>
</nav>
