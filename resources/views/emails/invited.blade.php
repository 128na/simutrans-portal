@include('emails.header', ['name' => $user->name])

<p>
    {{ $invited->name }}が{{ $user->name }}の招待URLからユーザー登録しました。
</p>
<p>
    招待した心当たりが無い場合、招待URLが漏洩している可能性がありますのでマイページから招待URLの再生成か削除を行ってください。<br>
    マイページ：{{ route('login') }}
</p>
@include('emails.footer')
