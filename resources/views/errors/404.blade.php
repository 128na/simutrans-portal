@extends('layouts.front')

@section('title', __('Not found'))

@section('content')
    <p>
        <a href="{{ route('index') }}">{{ __('Top') }}</a>
    </p>
@endsection
