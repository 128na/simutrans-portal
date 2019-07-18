<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang('Please click the button below to verify your email address.')
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a><br>
    @lang('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')])
</p>
<p>@lang('If you did not create an account, no further action is required.')</p>
