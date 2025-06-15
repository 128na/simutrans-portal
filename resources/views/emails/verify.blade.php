@include('emails.header', ['name' => $user->name])

<p>
    メールアドレスを確認するには、下のボタンをクリックしてください。
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a><br>
    このリンクは{{config('auth.passwords.users.expire')}}分間のみ有効です。
</p>
@include('emails.footer')
