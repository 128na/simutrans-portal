@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <div class="mb-12">
        <h2 class="v2-text-h2">ファイルの編集</h2>
        <p class="mt-2 text-c-sub">
            アップロード済みのファイルを編集できます
        </p>
    </div>
    <div class="pt-6 v2-page-content-area">
        <div id="app-attachment-edit">読み込み中...</div>
    </div>
    @endsection
