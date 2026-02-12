@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">二要素認証の設定</h2>
        <p class="text-c-sub">
            ログイン時に Google Authenticator などの多要素認証アプリによる追加認証を設定できます。<br />
            設定途中で中断すると正しく設定できない場合があります。設定失敗した場合は数時間経過するとリセットされます。
        </p>
    </div>
    <div class="v2-page-content-area-lg">
        @switch(true)

        @case(session('status') ===\Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED || $errors->getBag('confirmTwoFactorAuthentication')->has('code'))
        {{-- Step.2 設定開始 --}}
        {!! $user->twoFactorQrCodeSvg() !!}
        <p class="text-c-sub">
            表示されたQRコードを多要素認証アプリで読み取り、表示されたコードを入力してください。
        </p>
        <form action="{{route('two-factor.confirm')}}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="code" class="v2-form-caption">認証コード</label>
                @error('code', 'confirmTwoFactorAuthentication')
                <div class="v2-form-error">{{$message}}</div>
                @enderror
                <input id="code" type="code" name="code" autocomplete="one-time-code" class="v2-input w-full" />
            </div>
            <div>
                <button type="submit" class="v2-button v2-button-lg v2-v2-button v2-button-lg v2-button-primary w-full sm:w-64">
                    送信
                </button>
            </div>
        </form>
        @break

        @case(session('status') === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED || $user->two_factor_secret)
        {{-- Step. コード入力成功 または設定済み --}}
        <div>
            <h4 class="v2-text-h3 mb-4">リカバリコード</h4>
            <p class="text-c-sub mb-4">
                アプリが使用できなくなったときに使用できます。コードは安全な場所に保存してください。それぞれのコードは一度使用すると無効になります。
            </p>

            <textarea readonly class="v2-input w-full h-40 mb-4" onclick="this.select();">{{ implode("\n", $user->recoveryCodes()) }}</textarea>

            <form action="{{route('two-factor.regenerate-recovery-codes')}}" method="POST">
                @csrf
                <div class="gap-x-2 flex">
                    <button type="submit" class="v2-button v2-button-lg v2-button-sub">
                        再生成
                    </button>
                </div>
            </form>
        </div>
        <div>
            <h4 class="v2-text-h3 mb-4">二要素認証の無効化</h4>
            <form action="{{route('two-factor.disable')}}" method="POST" class="js-confirm" data-text="二要素認証を無効化しますか？">
                @csrf
                @method('DELETE')
                <button type="submit" class="v2-button v2-button-md v2-button-danger">
                    無効化
                </button>
            </form>
        </div>
        @break

        @default
        {{-- Step.1 未設定 --}}
        <form action="{{route('two-factor.enable')}}" method="POST">
            @csrf
            <button type="submit" class="v2-button v2-button-lg v2-button-primary">
                設定を始める
            </button>
        </form>
        @endswitch
    </div>
</div>

@endsection
