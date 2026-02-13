@extends('layouts.auth')

@section('max-w', 'v2-page-sm')
@section('page-content')
  <div class="v2-page v2-page-sm">
    <div class="mb-12">
      <h2 class="v2-text-h2">二要素認証</h2>
    </div>
    <form
      action="{{ route('two-factor.login.store') }}"
      method="POST"
      class="js-submit-once"
    >
      @csrf
      <div class="v2-page-content-area-md">
        <div>
          <label for="code" class="v2-form-caption">認証コード</label>
          @error('code')
            <div class="v2-form-error">{{ $message }}</div>
          @enderror

          <input
            id="code"
            type="code"
            name="code"
            autocomplete="one-time-code"
            class="v2-input w-full"
          />
        </div>
        <div>
          <label for="recovery_code" class="v2-form-caption">
            リカバリコード
          </label>
          @error('recovery_code')
            <div class="v2-form-error">{{ $message }}</div>
          @enderror

          <input
            id="recovery_code"
            type="recovery_code"
            name="recovery_code"
            class="v2-input w-full"
          />
          <p class="mt-2 text-sm text-c-sub">
            認証コードが利用できないときは、代わりにリカバリコードを使用してください。
          </p>
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
