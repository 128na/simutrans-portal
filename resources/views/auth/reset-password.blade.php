@extends('layouts.front')

@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">パスワードリセット</h2>
    </div>
    <form action="{{route('password.update')}}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <div class="v2-page-content-area">
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
                <label for="password" class="block text-sm/6 font-semibold">パスワード</label>
                <div class="mt-2.5">
                    <input id="password" type="password" name="password" value="{{old('password', '')}}" autocomplete="new-password" class="v2-input w-full" />
                </div>
                @error('password')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-c-primary px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-c-primary/80 w-full sm:w-64">
                    パスワードをリセット
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
