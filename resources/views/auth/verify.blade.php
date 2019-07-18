@extends('layouts.mypage')

@section('title', __('Verify Your Email Address'))

@section('content')
<div class="container">
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            @lang('A fresh verification link has been sent to your email address.')
        </div>
    @endif

    @lang('Before proceeding, please check your email for a verification link.')
    @lang('If you did not receive the email'), <a href="{{ route('verification.resend') }}">@lang('click here to request another')</a>.
</div>
@endsection
