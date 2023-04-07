<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ $user->name }}が{{ now()->format('Y/m/d H:i') }}にログインしました。
</p>
<p>
    == ログイン情報 ==
</p>
<p>
    IPアドレス<br>
    {{ request()->server('REMOTE_ADDR', '不明') }}<br>
    アクセス元<br>
    {{ request()->server('HTTP_REFERER', '不明') }}<br>
    ユーザーエージェント（ブラウザ情報）<br>
    {{ request()->server('HTTP_USER_AGENT', '不明') }}
</p>
