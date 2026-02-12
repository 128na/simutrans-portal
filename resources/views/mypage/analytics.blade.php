@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">アナリティクス</h2>
    </div>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div id="app-analytics" class="v2-page-content-area-lg">読み込み中...</div>
    @endsection
