@extends('layouts.mypage')

@section('title', __('auth.verify.email'))

@section('content')
<div class="container">
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            @lang('auth.verify.resent')
        </div>
    @endif

    @lang('auth.verify.attention')<br>
    @lang('auth.verify.not-received')<a href="{{ route('verification.resend') }}">@lang('auth.verify.resend')</a>
</div>
@endsection
