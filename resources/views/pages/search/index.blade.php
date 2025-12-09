@extends('layouts.front')

@section('max-w', '2-content-lg')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="v2-page v2-page-lg">
    @include('pages.search.options')
    <div id="app-article-list">読み込み中...</div>

    <div class="mt-10 v2-page-pagination-area">
        {{ $articles->withQueryString() }}
    </div>
</div>

@endsection
