<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang('email.reset.message_1')<br>
    @lang('email.reset.message_2')
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a>
</p>
