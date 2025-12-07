@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    @include('pages.search.options')
    <div id="app-article-list">読み込み中...</div>

    <div class="mt-10 border-t border-c-sub/10 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->withQueryString() }}
    </div>
</div>

@endsection
