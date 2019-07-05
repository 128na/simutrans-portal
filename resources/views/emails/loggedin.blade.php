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
    {{ __('email.loggedin.ip') }}: {{ env('REMOTE_ADDR', 'unknown') }}<br>
    {{ __('email.loggedin.referrer') }}: {{ env('HTTP_REFERER', 'unknown') }}<br>
    {{ __('email.loggedin.user-agent') }}: {{ env('HTTP_USER_AGENT', 'unknown') }}
</p>
