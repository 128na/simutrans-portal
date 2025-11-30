@extends('layouts.front')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-3xl font-semibold text-pretty text-gray-900">ユーザー登録</h2>
        <p class="mt-2 text-md text-gray-600">
            このコードは「{{$invitee->name}}」さんからの招待コードです。
        </p>
    </div>
    <form action="{{route('user.registration', $invitee->invitation_code)}}" method="POST">
        @csrf
        <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
            <div>
                <label for="name" class="block text-sm/6 font-semibold text-gray-900">名前</label>
                <p class="mt-2 text-sm text-gray-600">
                    記事やユーザー一覧に表示される名称です。
                </p>
                <div class="mt-2.5">
                    <input id="name" type="text" name="name" maxlength="100" autocomplete="name" value="{{old('name', '')}}" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('name')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm/6 font-semibold text-gray-900">メールアドレス</label>
                <div class="mt-2.5">
                    <input id="email" type="email" name="email" autocomplete="email" value="{{old('email', '')}}" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('email')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm/6 font-semibold text-gray-900">パスワード</label>
                <div class="mt-2.5">
                    <input id="password" type="password" name="password" minlength="11" value="{{old('password', '')}}" autocomplete="new-password" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-brand sm:w-128" />
                </div>
                @error('password')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="sm:mr-2 mr-4 sm:mb-1 mb-2 inline-block cursor-pointer">
                    <input class="accent-brand mr-0.5" type="checkbox" name="agreement" id="agreement" {{ old('agreement', '') ? 'checked' : '' }} />
                    @include('components.ui.link-external', ['url' => config('app.privacy_policy_url'), 'title' => 'プライバシーポリシー']) に同意します。
                </label>
                @error('agreement')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <button type="submit" class="rounded-md bg-brand px-8 sm:py-2 py-4 text-white cursor-pointer hover:bg-brand/90 w-full sm:w-64">
                    登録する
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
