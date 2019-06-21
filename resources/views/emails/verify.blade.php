<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ __('email.verify.message_1') }}<br>
    {{ __('email.verify.message_2') }}
</p>
<p>
    <a href="{{ $actionUrl }}">{{ $actionText }}</a>
</p>
