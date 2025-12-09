@extends('layouts.front')

@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">ログイン</h2>
    </div>
    <form action="{{route('login.store')}}" method="POST">
        @csrf
        <div class="v2-page-content-area">
            <div>
                @foreach ($errors->all() as $key => $error)
                <div class="text-sm text-c-danger">{{$error}}</div>
                @endforeach
                <label for="email" class="v2-form-caption">メールアドレス</label>
                <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="v2-input w-full mb-4" />
                <label for="password" class="v2-form-caption">パスワード</label>
                <input id="password" type="password" name="password" value="{{old('password', '')}}" autocomplete="current-password" class="v2-input w-full mb-4" />
                <button type="submit" class="v2-button v2-button-lg v2-button-primary w-full sm:w-64 mb-4">
                    ログイン
                </button>
                <div>
                    @include('components.ui.link', ['url' => route('forgot-password'), 'title' => 'パスワードをリセットするには？'])
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
