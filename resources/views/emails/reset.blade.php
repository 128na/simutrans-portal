@include('emails.header', ['name' => $user->name])

<p>
    パスワードリセットのリクエストを受け付けました。
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a><br>
    このリンクは{{config('auth.passwords.users.expire')}}分間のみ有効です。
</p>
@include('emails.footer')
