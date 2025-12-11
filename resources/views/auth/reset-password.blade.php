@extends('layouts.front')

@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">パスワードリセット</h2>
    </div>
    <form action="{{route('password.update')}}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <div class="v2-page-content-area-md">
            <div>
                <label for="email" class="v2-form-caption">メールアドレス</label>
                @error('email')
                <div class="v2-form-error">{{ $message }}</div>
                @enderror
                <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="v2-input w-full" required />
            </div>
            <div>
                <label for="password" class="v2-form-caption">パスワード</label>
                @error('password')
                <div class="v2-form-error">{{ $message }}</div>
                @enderror
                <input id="password" type="password" name="password" value="{{old('password', '')}}" autocomplete="new-password" class="v2-input w-full" required />
            </div>
            <div>
                <button type="submit" class="v2-button v2-button-lg v2-button-primary w-full sm:w-64">
                    パスワードをリセット
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
