@extends('layouts.front')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">二要素認証</h2>
    </div>
    <form action="{{route('two-factor.login.store')}}" method="POST">
        @csrf
        <div class="flex flex-col gap-y-4 border-t border-c-sub/10 pt-6 lg:mx-0">
            <div>
                <label for="code" class="block text-sm/6 font-semibold">認証コード</label>
                <div class="mt-2.5">
                    <input id="code" type="code" name="code" autocomplete="one-time-code" class="block w-full rounded-md px-3.5 py-2 text-base outline-1 -outline-offset-1 outline-c-sub/10 placeholder:text-c-sub focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:w-128" />
                </div>
                @error('code')
                <div class="text-sm text-c-danger">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label for="recovery_code" class="block text-sm/6 font-semibold">リカバリコード</label>
                <p class="mt-2 text-sm text-c-sub">
                    認証コードが利用できないときは、二要素認証登録時に発行したリカバリコードを使用してください。
                </p>
                <div class="mt-2.5">
                    <input id="recovery_code" type="recovery_code" name="recovery_code" class="block w-full rounded-md px-3.5 py-2 text-base outline-1 -outline-offset-1 outline-c-sub/10 placeholder:text-c-sub focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:w-128" />
                </div>
                @error('recovery_code')
                <div class="text-sm text-c-danger">{{$message}}</div>
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
