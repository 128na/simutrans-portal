@extends('layouts.front')

@section('max-w', '2-content-lg')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">お知らせ</h2>
        <p class="v2-page-text-sub">運営からのお知らせです。</p>
    </div>
    <div id="app-article-list">読み込み中...</div>
    <div class="mt-10 v2-page-pagination-area">
        {{ $articles->links() }}
    </div>
</div>

@endsection
