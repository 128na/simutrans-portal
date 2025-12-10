@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <script id="data-user" type="application/json">
        @json($user)

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
    <div id="app-article-create">読み込み中...</div>
    @endsection
