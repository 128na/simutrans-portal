@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
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
    <div class="flex flex-col gap-y-12 lg:mx-0">
        <div id="app-article-create">読み込み中...</div>
    </div>
    @endsection
