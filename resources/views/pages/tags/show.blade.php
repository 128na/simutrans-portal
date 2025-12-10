@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">タグ「{{$tag->name}}」の記事</h2>
        <pre class="v2-page-text-sub whitespace-pre-wrap break-all mb-2">{{$tag->description}}</pre>
        <div>
            @include('components.ui.link', ['url' => route('search', ['tagIds' => [$tag->id]]), 'title' => 'さらに検索条件を追加する'])
        </div>
    </div>
    <div id="app-article-list">読み込み中...</div>

    <div class="mt-10 v2-page-pagination-area">
        {{ $articles->links() }}
    </div>
</div>

@endsection
