<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang('You are receiving this email because we received a password reset request for your account.')<br>
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a><br>
    @lang('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')])
</p>
<p>@lang('If you did not request a password reset, no further action is required.')</p>
