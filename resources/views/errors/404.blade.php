@extends('layouts.front')

@section('title', __('Not found'))

@section('content')
    <h2>@lang('Not found')</h2>
    <p>
        <a href="{{ route('index') }}">@lang('Top')</a>
    </p>
@endsection
