@extends('layouts.front')

@section('title', 'ログイン')

@section('content')
    <div class="container mt-5">
        <h2>ログイン</h2>
        <form method="POST" action="{{ route('oauth.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">ログイン</button>
            </div>
        </form>
    </div>
