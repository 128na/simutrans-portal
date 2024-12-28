<?php

declare(strict_types=1);

?>
<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    メールアドレスを確認するには、下のボタンをクリックしてください。
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a><br>
    このリンクは{{config('auth.passwords.users.expire')}}分間のみ有効です。
</p>
<p>
    パスワードのリセットにお心当たりが無い場合は、このメールを無視してください。
</p>
<?php 
