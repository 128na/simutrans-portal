<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ __('email.reset.message_1') }}<br>
    {{ __('email.reset.message_2') }}
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a>
</p>
