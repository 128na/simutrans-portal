<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ __('email.loggedin.message_1', ['name' => $user->name, 'time' => now()]) }}
</p>
<p>
    {{ __('email.loggedin.message_2') }}
</p>
<p>
    {{ __('email.loggedin.ip') }}: {{ $_SERVER['REMOTE_ADDR'] }}<br>
    {{ __('email.loggedin.referrer') }}: {{ $_SERVER['HTTP_REFERER'] }}<br>
    {{ __('email.loggedin.user-agent') }}: {{ $_SERVER['HTTP_USER_AGENT'] }}
</p>
