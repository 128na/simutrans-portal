@extends('layouts.front')

@section('title', 'エラー')

@section('content')
    <h2>{{ $exception->getMessage() }}</h2>
    <p>
        <a href="{{ route('index') }}">トップ</a>
    </p>
@endsection
