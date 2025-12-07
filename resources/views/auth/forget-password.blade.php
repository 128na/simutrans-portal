@extends('layouts.front')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">パスワードリセット</h2>
        <p class="mt-2 text-md text-c-sub">
            ユーザー登録時に使用したメールアドレス宛にパスワード再設定用のリンクを送信します。
        </p>
    </div>
    <form action="{{route('password.email')}}" method="POST">
        @csrf
        <div class="flex flex-col gap-y-4 border-t border-c-sub/10 pt-6 lg:mx-0">
            <div>
                <label for="email" class="block text-sm/6 font-semibold">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="block w-full rounded-md px-3.5 py-2 text-base outline-1 -outline-offset-1 outline-c-sub/10 placeholder:text-c-sub focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:w-128" />
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
