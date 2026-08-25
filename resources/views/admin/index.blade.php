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
        <x-ui.link :url="route('admin.oauth.twitter.refresh')" :title="'トークンリフレッシュ'" />
        <br />
        <x-ui.link :url="route('admin.oauth.twitter.revoke')" :title="'トークン削除'" />
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
