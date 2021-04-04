@extends('layouts.front')

@section('title', 'ログインが必要です')

@section('content')
    <h2>ログインが必要です</h2>
    <p>
        この機能を使うにはログインとメールアドレスの認証が必要です。<br>
        <a class="btn btn-primary" href="{{ route('mypage.index') }}">ログイン</a>
    </p>
@endsection
