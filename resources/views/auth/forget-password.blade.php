@extends('layouts.front')

@section('max-w', 'v2-page-sm')
@section('page-content')
  <div class="v2-page v2-page-sm">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">パスワードリセット</h2>
      <p class="text-md text-c-sub">
        ユーザー登録時に使用したメールアドレス宛にパスワード再設定用のリンクを送信します。
      </p>
    </div>
    <form
      action="{{ route('password.email') }}"
      method="POST"
      class="js-submit-once"
    >
      @csrf
      <div class="v2-page-content-area-md">
        <div>
          <label for="email" class="v2-form-caption">メールアドレス</label>
          @error('email')
            <div class="v2-form-error">{{ $message }}</div>
          @enderror

          <input
            id="email"
            type="email"
            name="email"
            autocomplete="email"
            value="{{ old('email', '') }}"
            class="v2-input w-full"
            required
          />
        </div>
        <div>
          <button
            type="submit"
            class="v2-button v2-button-lg v2-button-primary w-full sm:w-64"
          >
            送信
          </button>
        </div>
      </div>
    </form>
  </div>
@endsection
