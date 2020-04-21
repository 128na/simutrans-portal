@extends('layouts.front')

@section('title', __('An error has occurred'))

@section('content')
    <h2>@lang('An error has occurred')</h2>
    <p>
        <a href="{{ route('index') }}">@lang('Top')</a>
    </p>
@endsection
