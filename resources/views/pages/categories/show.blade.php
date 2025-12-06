@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">
            @lang("category.pak.{$pak->slug}") / @lang("category.addon.{$addon->slug}") の記事
        </h2>
        <div class="mt-2">
            @include('components.ui.link', ['url' => route('search', ['categoryIds' => [$pak->id, $addon->id]]), 'title' => 'さらに検索条件を追加する'])
        </div>
    </div>
    <div id="app-article-list"></div>

    <div class="mt-10 border-t border-g2 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->links() }}
    </div>
</div>

@endsection
