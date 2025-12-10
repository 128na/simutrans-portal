@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">二要素認証の設定</h2>
        <p class="text-c-sub">
            ログイン時に Google Authenticator などの多要素認証アプリによる追加認証を設定できます。
        </p>
    </div>
    <div class="v2-page-content-area-lg">
        @switch(true)

        @case(session('status') ===\Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED || $errors->getBag('confirmTwoFactorAuthentication')->has('code'))
        {{-- Step.2 設定開始 --}}
        {!! $user->twoFactorQrCodeSvg() !!}
        <p class="mt-2 text-c-sub">
            表示されたQRコードを多要素認証アプリで読み取り、表示されたコードを入力してください。
        </p>
        <form action="{{route('two-factor.confirm')}}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="code" class="v2-form-caption">認証コード</label>
                <div class="mt-2.5">
                    <input id="code" type="code" name="code" autocomplete="one-time-code" class="v2-input w-full" />
                </div>
                @error('code', 'confirmTwoFactorAuthentication')
                <div class="v2-form-error">{{$message}}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="v2-button v2-button-lg v2-button-primary w-full sm:w-64">
                    送信
                </button>
            </div>
        </form>
        @break

        @case(session('status') === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED || $user->two_factor_confirmed_at)
        {{-- Step. コード入力成功 または設定済み --}}
        <div>
            <h4 class="v2-text-h3">リカバリコード</h4>
            <p class="my-2 text-c-sub">
                アプリが使用できなくなったときに使用できます。コードは安全な場所に保存してください。それぞれのコードは一度使用すると無効になります。
            </p>

            <p class="p-4 mb-4 text-sm rounded-lg bg-gray-50 border border-c-sub/10 ">
                @foreach ($user->recoveryCodes() as $recoveryCode)
                {{ $recoveryCode }}<br>
                @endforeach
            </p>
            <form action="{{route('two-factor.regenerate-recovery-codes')}}" method="POST">
                @csrf
                <div class="gap-x-2 flex">
                    <button type="submit" class="button-sub js-clipboard" data-text="{{implode(" ", $user->recoveryCodes())}}">
                        コードをコピー
                    </button>
                    <button type="submit" class="button-primary">
                        再生成
                    </button>
                </div>
            </form>

            <h4 class="v2-text-h3">二要素認証の無効化</h4>
            <form action="{{route('two-factor.disable')}}" method="POST" class="js-confirm" data-text="二要素認証を無効化しますか？">
                @csrf
                @method('DELETE')
                <button type="submit" class="button-danger">
                    無効化
                </button>
            </form>
        </div>
        @break

        @default
        {{-- Step.1 未設定 --}}
        <form action="{{route('two-factor.enable')}}" method="POST">
            @csrf
            <button type="submit" class="button-primary">
                設定を始める
            </button>
        </form>
        @endswitch
    </div>
</div>

@endsection
