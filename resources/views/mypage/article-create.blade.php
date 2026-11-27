@extends('layouts.mypage')
{{-- 編集欄とプレビューを並べるため、画面幅いっぱいを使う。.v2-header の上限に勝つよう ! を付ける --}}
@section('max-w', 'max-w-none')
@section('layout-max-w', 'max-w-none!')
@section('page-content')
  <div class="v2-page max-w-none">
    <script id="data-user" type="application/json">
      @json($user)
    </script>
    <script id="data-attachments" type="application/json">
      @json($attachments)
    </script>
    <script id="data-categories" type="application/json">
      @json($categories)
    </script>
    <script id="data-tags" type="application/json">
      @json($tags)
    </script>
    <script id="data-relational-articles" type="application/json">
      @json($relationalArticles)
    </script>
    <div id="app-article-create">読み込み中...</div>
  </div>
@endsection
