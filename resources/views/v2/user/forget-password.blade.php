@extends('v2.parts.layout')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">パスワードリセット</h2>
        <p class="mt-2 text-md text-gray-600">
            ユーザー登録時に使用したメールアドレス宛にパスワード再設定用のリンクを送信します。
        </p>
    </div>
    <form action="{{route('password.email')}}" method="POST">
        @csrf
        <div class="mt-10 flex flex-col gap-y-6 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
            <div>
                <label for="email" class="block text-sm/6 font-semibold text-gray-900">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('email')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-brand px-8 sm:py-2 py-4 text-white cursor-pointer w-full sm:w-64">
                    送信
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
