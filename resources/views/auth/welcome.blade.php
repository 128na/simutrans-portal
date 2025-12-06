@extends('layouts.front')

@section('max-w', 'max-w-xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">ユーザー登録完了</h2>
        <p class="mt-2 text-md text-secondary">
            ようこそ「{{$inviter->name}}」さん。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-muted pt-6 lg:mx-0">
        <p class="p-4 mb-4 text-sm text-yellow-900 rounded-lg bg-yellow-50 border border-yellow-300 ">
            記事の投稿などすべての機能を使うには<strong>メールアドレスの確認</strong>が必要です。<br>
            登録したメールアドレスへ後ほど送られてくるメールからメールアドレスの確認を行ってください。
        </p>
        <p class="">
            ログインは @include('components.ui.link', ['url' => route('login'), 'title' => 'マイページ']) からできます。<br>
        </p>
    </div>
</div>

@endsection
