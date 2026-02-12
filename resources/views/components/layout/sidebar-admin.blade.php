<nav aria-label="Sidebar" class="space-y-4">
    <ul class="space-y-4">
        <li>@include('components.ui.link', ['url' => route('admin.index'), 'title' => '管理トップ'])</li>
        <li>
        X連携
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('admin.oauth.twitter.authorize'), 'title' => '認証'])</li>
            <li>@include('components.ui.link', ['url' => route('admin.oauth.twitter.refresh'), 'title' => 'トークンリフレッシュ'])</li>
            <li>@include('components.ui.link', ['url' => route('admin.oauth.twitter.revoke'), 'title' => 'トークン削除'])</li>
        </ul>
        </li>
        <li>
        ドキュメント
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('l5-swagger.default.api'), 'title' => 'APIドキュメント'])</li>
        </ul>
        </li>
        <li>
        その他
        <ul class="ml-3 space-y-2">
            <li>@include('components.ui.link', ['url' => route('index'), 'title' => 'トップページ'])</li>
            <li>@include('components.ui.link', ['url' => route('mypage.index'), 'title' => 'マイページ'])</li>
        </ul>
        </li>
    </ul>
</nav>
