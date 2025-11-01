@extends('v2.mypage.layout')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">記事の一覧</h2>
    </div>
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <div id="app-article-list"></div>
    </div>
    @endsection
