@extends('v2.parts.layout')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">二要素認証</h2>
    </div>
    <form action="{{route('two-factor.login.store')}}" method="POST">
        @csrf
        <div class="mt-10 flex flex-col gap-y-6 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
            <div>
                <label for="code" class="block text-sm/6 font-semibold text-gray-900">認証コード</label>
                <div class="mt-2.5">
                    <input id="code" type="code" name="code" autocomplete="one-time-code" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('code')
                <div class="text-sm text-red-600">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label for="recovery_code" class="block text-sm/6 font-semibold text-gray-900">リカバリコード</label>
                <p class="mt-2 text-sm text-gray-600">
                    認証コードが利用できないときは、二要素認証登録時に発行したリカバリコードを使用してください。
                </p>
                <div class="mt-2.5">
                    <input id="recovery_code" type="recovery_code" name="recovery_code" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('recovery_code')
                <div class="text-sm text-red-600">{{$message}}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-brand px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-brand/90 w-full sm:w-64">
                    送信
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
