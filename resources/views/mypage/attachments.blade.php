@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">ファイルの編集</h2>
        <p class="text-c-sub">
            アップロード済みのファイルを編集できます
        </p>
    </div>
    <div id="app-attachment-edit">読み込み中...</div>
    @endsection
