<?php

declare(strict_types=1);

?>
<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ $invited->name }}が{{ $user->name }}の招待URLからユーザー登録しました。
</p>
<p>
    招待した心当たりが無い場合、招待URLが漏洩している可能性がありますのでマイページから招待URLの再生成か削除を行ってください。<br>
    マイページ：{{ route('mypage.index') }}
</p>
<?php 
