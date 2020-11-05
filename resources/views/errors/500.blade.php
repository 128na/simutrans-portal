@extends('layouts.front')

@section('title', 'システムエラー')

@section('content')
    <h2>システムエラーが発生しました。</h2>
    <p>
        <a href="{{ route('index') }}">トップ</a>
    </p>
@endsection
