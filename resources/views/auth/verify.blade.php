@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.verify.email') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.verify.resent') }}
                        </div>
                    @endif

                    {{ __('auth.verify.attention') }}<br>
                    {{ __('auth.verify.not-received') }}<a href="{{ route('verification.resend') }}">{{ __('auth.verify.resend') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
