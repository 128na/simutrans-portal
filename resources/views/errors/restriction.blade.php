@extends('layouts.front')

@section('title', '認証エラー')

@section('content')
    <h2>指定されたアカウント情報は登録・ログインに使用できません。</h2>
    <p>
        <a href="{{ route('mypage.index') }}">ログインページ</a>
    </p>
@endsection
