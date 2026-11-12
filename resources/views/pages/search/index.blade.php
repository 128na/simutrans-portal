@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    @include('pages.search.options')
    @if ($hasSearchConditions)
      <div class="mb-6">
        {{-- 検索結果は種類ごとの件数を出せないので、決めておいた主要な種類だけを出して残りを畳む --}}
        @include('components.partials.article-list-filter', [
          'filter' => $filter,
          'total' => $articles->total(),
          'acquisitionAll' => ($condition['postTypes'] ?? []) === [],
          'acquisitionDownloadOnly' => ($condition['postTypes'] ?? []) === [\App\Enums\ArticlePostType::AddonPost->value],
          'addonPrimary' => \App\Services\Front\ArticleListFilter::SEARCH_PRIMARY_ADDONS,
        ])
      </div>
    @endif
    @if (! $hasSearchConditions)
      <p class="v2-text-sub">キーワードを入力するか、詳細条件を指定して検索してください。</p>
    @elseif ($articles->total() === 0)
      <div class="space-y-2 v2-text-sub">
        <p>条件に一致する記事が見つかりませんでした。</p>
        <p>キーワードを短くするか、条件を減らして再検索してください。</p>
      </div>
    @elseif ($articles->count() === 0)
      <p class="v2-text-sub">
        このページに記事はありません。
        <x-ui.link :url="$articles->withQueryString()->url(1)" title="検索結果の最初のページへ" />
      </p>
    @else
      <script id="data-articles" type="application/json">
        @json($articles)
      </script>
      <div id="app-article-cards" data-sort="{{ $filter->sort->value }}">読み込み中...</div>

      <div class="mt-10 v2-page-pagination-area">
        {{ $articles->withQueryString()->onEachSide(1)->links() }}
      </div>
    @endif
  </div>
@endsection
