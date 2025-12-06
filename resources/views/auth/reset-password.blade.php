@extends('layouts.front')

@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">パスワードリセット</h2>
    </div>
    <form action="{{route('password.update')}}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
            <div>
                <label for="email" class="block text-sm/6 font-semibold text-primary">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-primary outline-1 -outline-offset-1 outline-gray-300 placeholder:text-muted focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('email')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm/6 font-semibold text-primary">パスワード</label>
                <div class="mt-2.5">
                    <input id="password" type="password" name="password" value="{{old('password', '')}}" autocomplete="new-password" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-primary outline-1 -outline-offset-1 outline-gray-300 placeholder:text-muted focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('password')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-brand px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-brand/90 w-full sm:w-64">
                    パスワードをリセット
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
