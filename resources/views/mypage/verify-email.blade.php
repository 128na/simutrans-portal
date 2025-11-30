@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">メールアドレスの検証</h2>
        <p class="mt-2 text-gray-600">
            登録に使用したメールアドレスが有効なものか検証します。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        <form action="{{route('verification.send')}}" method="POST">
            @csrf
            <button type="submit" class="rounded-md bg-brand px-4 sm:py-2 py-4 text-white cursor-pointer">
                送信する
            </button>
        </form>
    </div>
</div>

@endsection
