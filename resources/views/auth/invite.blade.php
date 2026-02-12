@extends('layouts.front')

@section('max-w', 'v2-page-sm')
@section('page-content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">ユーザー登録</h2>
        <p class="text-md text-c-sub">
            このコードは「{{$invitee->name}}」さんからの招待コードです。
        </p>
    </div>
    <form action="{{route('user.registration', $invitee->invitation_code)}}" method="POST" class="js-submit-once">
        @csrf
        <div class="v2-page-content-area-md">
            <div>
                <label for="name" class="v2-form-caption">名前</label>
                @error('name')
                <div class="v2-form-error">{{ $message }}</div>
                @enderror
                <input id="name" type="text" name="name" maxlength="100" autocomplete="name" value="{{old('name', '')}}" class="v2-input w-full" required />
                <p class="mt-2 text-sm text-c-sub">
                    記事やユーザー一覧に表示される名称です。
                </p>
            </div>
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
                <input id="password" type="password" name="password" minlength="11" value="{{old('password', '')}}" autocomplete="new-password" class="v2-input w-full" required />
            </div>
            <div>
                <label>
                    <input class="v2-checkbox peer" type="checkbox" name="agreement" id="agreement" {{ old('agreement', '') ? 'checked' : '' }} required />
                    <span class="v2-checkbox-label">
                        @include('components.ui.link', ['url' => config('app.privacy_policy_url'), 'title' => 'プライバシーポリシー']) に同意します。
                    </span>
                </label>
                @error('agreement')
                <div class="v2-form-error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="v2-button v2-button-lg v2-button-primary w-full sm:w-64">
                    登録する
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
