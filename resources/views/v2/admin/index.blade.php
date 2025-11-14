@extends('v2.mypage.layout')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">管理</h2>
    </div>
    <div class="mt-10 flex flex-col gap-y-4 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <div>
            @include('v2.parts.link', ['url'=>"/admin/oauth/twitter/authorize", 'title' =>'認証'])<br>
            @include('v2.parts.link', ['url'=>"/admin/oauth/twitter/refresh", 'title' =>'トークンリフレッシュ'])<br>
            @include('v2.parts.link', ['url'=>"/admin/oauth/twitter/revoke", 'title' =>'トークン削除'])<br>
        </div>
    </div>
</div>
@endsection
