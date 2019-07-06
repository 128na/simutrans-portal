@extends('layouts.mypage')

@section('title', __('auth.verify.email'))

@section('content')
<div class="container">
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('auth.verify.resent') }}
        </div>
    @endif

    {{ __('auth.verify.attention') }}<br>
    {{ __('auth.verify.not-received') }}<a href="{{ route('verification.resend') }}">{{ __('auth.verify.resend') }}</a>
</div>
@endsection
