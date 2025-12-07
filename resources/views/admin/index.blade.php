@extends('layouts.admin')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">管理</h2>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-c-sub/10 pt-6 lg:mx-0">
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
