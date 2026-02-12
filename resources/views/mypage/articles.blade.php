@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div class="mb-12">
        <h2 class="v2-text-h2">記事一覧</h2>
    </div>
    <div id="app-article-list">読み込み中...</div>
    @endsection
