@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">アナリティクス</h2>
    </div>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div class="v2-page-content-area">
        <div id="app-analytics">読み込み中...</div>
    </div>
    @endsection
