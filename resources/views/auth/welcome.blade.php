@extends('layouts.front')

@section('max-w', '2-content-sm')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">ユーザー登録完了</h2>
        <p class="mt-2 text-md text-c-sub">
            ようこそ「{{$inviter->name}}」さん。
        </p>
    </div>
    <div class="pt-6 v2-page-content-area">
        <p class="p-4 mb-4 text-sm text-warn-dark rounded-lg bg-c-warn-light border border-warn-light ">
            記事の投稿などすべての機能を使うには<strong>メールアドレスの確認</strong>が必要です。<br>
            登録したメールアドレスへ後ほど送られてくるメールからメールアドレスの確認を行ってください。
        </p>
        <p class="">
            ログインは @include('components.ui.link', ['url' => route('login'), 'title' => 'マイページ']) からできます。<br>
        </p>
    </div>
</div>

@endsection
