@extends('layouts.front')

@section('title', 'ページが見つかりません')

@section('content')
    <h2>ページが見つかりませんでした。</h2>
    <p>
        <a href="{{ route('index') }}">トップ</a>
    </p>
@endsection
