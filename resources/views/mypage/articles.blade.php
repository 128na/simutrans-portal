@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div class="mb-6">
        <h2 class="title-xl">記事の一覧</h2>
    </div>
    <div class="flex flex-col gap-y-12 border-t border-g2 pt-6 lg:mx-0">
        <div id="app-article-list"></div>
    </div>
    @endsection
