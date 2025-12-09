@extends('layouts.front')

@section('max-w', '2-content-sm')
@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">二要素認証</h2>
    </div>
    <form action="{{route('two-factor.login.store')}}" method="POST">
        @csrf
        <div class="v2-page-content-area">
            <div>
                <label for="code" class="block text-sm/6 font-semibold">認証コード</label>
                <div class="mt-2.5">
                    <input id="code" type="code" name="code" autocomplete="one-time-code" class="v2-input w-full" />
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
                    <input id="recovery_code" type="recovery_code" name="recovery_code" class="v2-input w-full" />
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
