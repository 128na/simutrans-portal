@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">タグの編集</h2>
        <p class="text-c-sub">
            タグの作成、作成済みタグの説明文を編集できます。
        </p>
    </div>
    <script id="data-tags" type="application/json">
        @json($tags)

    </script>
    <div class="v2-page-content-area-lg">
        <div id="app-tag-edit">読み込み中...</div>
    </div>
    @endsection
