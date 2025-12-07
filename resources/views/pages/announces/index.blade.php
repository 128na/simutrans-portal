@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">お知らせ</h2>
        <p class="mt-2 text-lg/8 text-c-sub">運営からのお知らせです。</p>
    </div>
    <div id="app-article-list"></div>
    <div class="mt-10 border-t border-c-sub/10 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->links() }}
    </div>
</div>

@endsection
