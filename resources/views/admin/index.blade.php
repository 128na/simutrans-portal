@extends('layouts.admin')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h2">管理</h2>
    </div>
    <div class="v2-page-content-area-lg">
      <div>
        <x-ui.link :url="route('admin.oauth.twitter.authorize')" :title="'認証'" />
        <br />
        <form action="{{ route('admin.oauth.twitter.refresh') }}" method="POST">
          @csrf
          <button type="submit" class="v2-button v2-button-md v2-button-sub">トークンリフレッシュ</button>
        </form>
        <br />
        <form action="{{ route('admin.oauth.twitter.revoke') }}" method="POST" class="js-confirm" data-text="トークンを削除しますか？">
          @csrf
          <button type="submit" class="v2-button v2-button-md v2-button-danger">トークン削除</button>
        </form>
        <br />
      </div>
      @if (Illuminate\Support\Facades\Route::has('l5-swagger.default.api'))
        <div>
          <x-ui.link :url="route('l5-swagger.default.api')" :title="'APIドキュメント'" />
          <br />
        </div>
      @endif
    </div>
  </div>
@endsection
