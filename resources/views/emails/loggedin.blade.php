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
    {{ $loginHistory->ip ?? '不明' }}<br>
    アクセス元<br>
    {{ $loginHistory->referer ?? '不明' }}
    ユーザーエージェント（ブラウザ情報）<br>
    {{ $loginHistory->ua ?? '不明' }}<br>
</p>
