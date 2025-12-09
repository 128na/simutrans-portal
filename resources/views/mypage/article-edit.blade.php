@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-article" type="application/json">
        @json($article)

    </script>
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <script id="data-categories" type="application/json">
        @json($categories)

    </script>
    <script id="data-tags" type="application/json">
        @json($tags)

    </script>
    <script id="data-relational-articles" type="application/json">
        @json($relationalArticles)

    </script>
    <div class="flex flex-col gap-y-12 lg:mx-0">
        <div id="app-article-edit">読み込み中...</div>
    </div>
    @endsection
