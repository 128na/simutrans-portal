@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">タグの編集</h2>
        <p class="mt-2 text-gray-600">
            タグの作成、作成済みタグの説明文を編集できます。
        </p>
    </div>
    <script id="data-tags" type="application/json">
        @json($tags)

    </script>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        <div id="app-tag-edit"></div>
    </div>
    @endsection
