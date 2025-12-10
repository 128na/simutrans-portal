@extends('layouts.front')

@section('max-w', 'v2-page-sm')
@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">二要素認証</h2>
    </div>
    <form action="{{route('two-factor.login.store')}}" method="POST">
        @csrf
        <div class="v2-page-content-area-md">
            <div>
                <label for="code" class="v2-form-caption">認証コード</label>
                <input id="code" type="code" name="code" autocomplete="one-time-code" class="v2-input w-full" />
                @error('code')
                <div class="v2-form-error">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label for="recovery_code" class="v2-form-caption">リカバリコード</label>
                <input id="recovery_code" type="recovery_code" name="recovery_code" class="v2-input w-full" />
                @error('recovery_code')
                <div class="v2-form-error">{{$message}}</div>
                @enderror
                <p class="mt-2 text-sm text-c-sub">
                    認証コードが利用できないときは、代わりにリカバリコードを使用してください。
                </p>
            </div>
            <div>
                <button type="submit" class="v2-button v2-button-lg v2-button-primary w-full sm:w-64">
                    送信
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
