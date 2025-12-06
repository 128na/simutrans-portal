@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">二要素認証の設定</h2>
        <p class="mt-2 text-g5">
            ログイン時に Google Authenticator などの多要素認証アプリによる追加認証を設定できます。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-g2 pt-6 lg:mx-0">
        @switch(true)

        @case(session('status') ===\Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED || $errors->getBag('confirmTwoFactorAuthentication')->has('code'))
        {{-- Step.2 設定開始 --}}
        {!! $user->twoFactorQrCodeSvg() !!}
        <p class="mt-2 text-g5">
            表示されたQRコードを多要素認証アプリで読み取り、表示されたコードを入力してください。
        </p>
        <form action="{{route('two-factor.confirm')}}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="code" class="block text-sm/6 font-semibold text-g9">認証コード</label>
                <div class="mt-2.5">
                    <input id="code" type="code" name="code" autocomplete="one-time-code" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-g9 outline-1 -outline-offset-1 outline-g4 placeholder:text-g4 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('code', 'confirmTwoFactorAuthentication')
                <div class="text-sm text-danger">{{$message}}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-brand px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-brand/90 w-full sm:w-64">
                    送信
                </button>
            </div>
        </form>
        @break

        @case(session('status') === \Laravel\Fortify\Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED || $user->two_factor_confirmed_at)
        {{-- Step. コード入力成功 または設定済み --}}
        <div>
            <h4 class="title-md">リカバリコード</h4>
            <p class="my-2 text-g5">
                アプリが使用できなくなったときに使用できます。コードは安全な場所に保存してください。それぞれのコードは一度使用すると無効になります。
            </p>

            <p class="p-4 mb-4 text-sm text-g9 rounded-lg bg-gray-50 border border-g3 ">
                @foreach ($user->recoveryCodes() as $recoveryCode)
                {{ $recoveryCode }}<br>
                @endforeach
            </p>
            <form action="{{route('two-factor.regenerate-recovery-codes')}}" method="POST">
                @csrf
                <div class="gap-x-2 flex">
                    <button type="submit" class="rounded-md bg-g5 px-4 sm:py-2 py-4 text-white cursor-pointer js-clipboard" data-text="{{implode(" ", $user->recoveryCodes())}}">
                        コードをコピー
                    </button>
                    <button type="submit" class="rounded-md bg-brand px-4 sm:py-2 py-4 text-white cursor-pointer">
                        再生成
                    </button>
                </div>
            </form>

            <h4 class="title-md">二要素認証の無効化</h4>
            <form action="{{route('two-factor.disable')}}" method="POST" class="js-confirm" data-text="二要素認証を無効化しますか？">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md bg-danger px-4 sm:py-2 py-4 text-white cursor-pointer">
                    無効化
                </button>
            </form>
        </div>
        @break

        @default
        {{-- Step.1 未設定 --}}
        <form action="{{route('two-factor.enable')}}" method="POST">
            @csrf
            <button type="submit" class="rounded-md bg-brand px-4 sm:py-2 py-4 text-white cursor-pointer">
                設定を始める
            </button>
        </form>
        @endswitch
    </div>
</div>

@endsection
