@extends('layouts.front')

@section('title', __('Not found'))

@section('content')
    <p>
        <a href="{{ route('index') }}">@lang('Top')</a>
    </p>
@endsection
