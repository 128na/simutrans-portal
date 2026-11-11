@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('hide-sidebar', '1')
@section('page-content')
  <script id="data-article" type="application/json">
    @json($article)
  </script>
  <div class="v2-page v2-page-lg mb-32">
    <nav aria-label="現在位置" class="mb-3">
      <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-note text-c-sub">
        @foreach ($breadcrumbs as $breadcrumb)
          <li class="flex items-center gap-x-2">
            @unless ($loop->first)
              <span aria-hidden="true">/</span>
            @endunless
            <a href="{{ $breadcrumb['url'] }}" class="text-c-sub hover:text-c-link hover:underline">{{ $breadcrumb['title'] }}</a>
          </li>
        @endforeach
      </ol>
    </nav>
    <h2 class="v2-text-h2 mb-3 wrap-break-word">{{ $article->title }}</h2>
    <div id="app-article-show">読み込み中...</div>
  </div>
@endsection
