@extends('layouts.front')

@section('max-w', '2-content-sm')
@section('content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2">ユーザー登録</h2>
        <p class="mt-2 text-md text-c-sub">
            このコードは「{{$invitee->name}}」さんからの招待コードです。
        </p>
    </div>
    <form action="{{route('user.registration', $invitee->invitation_code)}}" method="POST">
        @csrf
        <div class="v2-page-content-area">
            <div>
                <label for="name" class="block text-sm/6 font-semibold">名前</label>
                <p class="mt-2 text-sm text-c-sub">
                    記事やユーザー一覧に表示される名称です。
                </p>
                <div class="mt-2.5">
                    <input id="name" type="text" name="name" maxlength="100" autocomplete="name" value="{{old('name', '')}}" class="v2-input w-full" />
                </div>
                @error('name')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm/6 font-semibold">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="v2-input w-full" />
                </div>
                @error('email')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm/6 font-semibold">パスワード</label>
                <div class="mt-2.5">
                    <input id="password" type="password" name="password" minlength="11" value="{{old('password', '')}}" autocomplete="new-password" class="v2-input w-full" />
                </div>
                @error('password')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="sm:mr-2 mr-4 sm:mb-1 mb-2 inline-block cursor-pointer">
                    <input class="accent-primary mr-0.5" type="checkbox" name="agreement" id="agreement" {{ old('agreement', '') ? 'checked' : '' }} />
                    @include('components.ui.link', ['url' => config('app.privacy_policy_url'), 'title' => 'プライバシーポリシー']) に同意します。
                </label>
                @error('agreement')
                <div class="text-sm text-c-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-c-primary px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-c-primary/80 w-full sm:w-64">
                    登録する
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
