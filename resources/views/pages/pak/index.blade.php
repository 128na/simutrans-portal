@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <script id="data-articles" type="application/json">
    @json($articles)
  </script>
  <div class="v2-page v2-page-lg">
    {{-- 一覧を 1 画面に多く見せるため、検索への導線は見出しの横に置き、pak の説明は一覧の後ろに回す --}}
    <div class="mb-3 flex flex-wrap items-baseline gap-x-4 gap-y-1">
      <h2 class="v2-text-h1">@lang("category.pak.{$pak}")</h2>
      <x-ui.link :url="route('search', ['categoryIds' => $categoryIds])" :title="'さらに検索条件を追加する'" />
    </div>
    <div class="mb-4">
      @include('components.partials.article-list-filter', ['filter' => $filter, 'total' => $articles->total(), 'addonCounts' => $addonCounts ?? null])
    </div>
    <div id="app-article-cards" data-sort="{{ $filter->sort->value }}">読み込み中...</div>
    <div class="mt-10 v2-page-pagination-area">
      {{ $articles->withQueryString()->onEachSide(1)->links() }}
    </div>
    <p class="mt-6 text-body text-c-sub">
      @include('pages.pak.description', ['pak' => $pak])
    </p>
  </div>
@endsection
