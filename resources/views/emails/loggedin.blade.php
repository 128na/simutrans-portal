<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang(':name logged in at :time.', ['name' => $user->name, 'time' => now()])
</p>
<p>
    @lang('== Login information ==')
</p>
<p>
    @lang('IP Address')<br>
    {{ env('REMOTE_ADDR', 'unknown') }}<br>
    @lang('Referrer')<br>
    {{ env('HTTP_REFERER', 'unknown') }}<br>
    @lang('User Agent')<br>
    {{ env('HTTP_USER_AGENT', 'unknown') }}
</p>
