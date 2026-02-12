@extends('layouts.front')

@section('max-w', 'v2-page-sm')
@section('page-content')
  <div class="v2-page v2-page-sm">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">ユーザー登録完了</h2>
      <p class="text-md text-c-sub">ようこそ「{{ $inviter->name }}」さん。</p>
    </div>
    <div class="v2-page-content-area-md">
      <div class="v2-card v2-card-primary">
        記事の投稿などすべての機能を使うには
        <strong>メールアドレスの確認</strong>
        が必要です。
        <br />
        登録したメールアドレスへ後ほど送られてくるメールからメールアドレスの確認を行ってください。
      </div>
      <div>
        ログインは
        @include('components.ui.link', ['url' => route('login'), 'title' => 'マイページ'])
        からできます。
        <br />
      </div>
    </div>
  </div>
@endsection
