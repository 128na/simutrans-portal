@extends('layouts.front')

@section('max-w', '2-content-sm')
@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">パスワードリセット</h2>
        <p class="mt-2 text-md text-c-sub">
            ユーザー登録時に使用したメールアドレス宛にパスワード再設定用のリンクを送信します。
        </p>
    </div>
    <form action="{{route('password.email')}}" method="POST">
        @csrf
        <div class="pt-6 v2-page-content-area">
            <div>
                <label for="email" class="block text-sm/6 font-semibold">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="v2-input w-full" />
                </div>
                @error('email')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-c-primary px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-c-primary/80 w-full sm:w-64">
                    送信
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
