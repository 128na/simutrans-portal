<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang('email.loggedin.message_1', ['name' => $user->name, 'time' => now()])
</p>
<p>
    @lang('email.loggedin.message_2')
</p>
<p>
    @lang('email.loggedin.ip'): {{ env('REMOTE_ADDR', 'unknown') }}<br>
    @lang('email.loggedin.referrer'): {{ env('HTTP_REFERER', 'unknown') }}<br>
    @lang('email.loggedin.user-agent'): {{ env('HTTP_USER_AGENT', 'unknown') }}
</p>
