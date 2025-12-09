@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">タグの編集</h2>
        <p class="mt-2 text-c-sub">
            タグの作成、作成済みタグの説明文を編集できます。
        </p>
    </div>
    <script id="data-tags" type="application/json">
        @json($tags)

    </script>
    <div class="pt-6 v2-page-content-area">
        <div id="app-tag-edit">読み込み中...</div>
    </div>
    @endsection
