@extends('layouts.front')

@section('title', __('message.not-found'))

@section('content')
    <p>
        <a href="{{ route('index') }}">{{ __('message.top') }}</a>
    </p>
@endsection
