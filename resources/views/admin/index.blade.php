@extends('layouts.admin')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">管理</h2>
    </div>
    <div class="v2-page-content-area">
        <div>
            @include('components.ui.link', ['url'=>route('admin.oauth.twitter.authorize'), 'title' =>'認証'])<br>
            @include('components.ui.link', ['url'=>route('admin.oauth.twitter.refresh'), 'title' =>'トークンリフレッシュ'])<br>
            @include('components.ui.link', ['url'=>route('admin.oauth.twitter.revoke'), 'title' =>'トークン削除'])<br>
        </div>
        <div>
            @include('components.ui.link', ['url'=>route('l5-swagger.default.api'), 'title' =>'APIドキュメント'])<br>
        </div>
    </div>
</div>
@endsection
