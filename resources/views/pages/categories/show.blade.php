@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <script id="data-articles" type="application/json">
    @json($articles)
  </script>
  <div class="v2-page v2-page-lg">
    <div class="mb-6">
      <h2 class="v2-text-h1">
        @lang("category.pak.{$pak->slug}")
        /
        @lang("category.addon.{$addon->slug}")
        の記事
      </h2>
      <div class="mt-2">
        <x-ui.link :url="route('search', ['categoryIds' => [$pak->id, $addon->id]])" :title="'さらに検索条件を追加する'" />
      </div>
    </div>
    <div class="mb-6">
      @include('components.partials.article-list-filter', ['filter' => $filter, 'total' => $articles->total(), 'showAddon' => false])
    </div>
    <div id="app-article-cards" data-sort="{{ $filter->sort->value }}">読み込み中...</div>

    <div class="mt-10 v2-page-pagination-area">
      {{ $articles->withQueryString()->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
