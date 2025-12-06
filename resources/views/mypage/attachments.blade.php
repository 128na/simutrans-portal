@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <div class="mb-6">
        <h2 class="title-xl">ファイルの編集</h2>
        <p class="mt-2 text-secondary">
            アップロード済みのファイルを編集できます
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-muted pt-6 lg:mx-0">
        <div id="app-attachment-edit"></div>
    </div>
    @endsection
