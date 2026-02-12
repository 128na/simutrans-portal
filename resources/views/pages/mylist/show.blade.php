@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    {{-- ヘッダー情報 --}}
    <div class="v2-card v2-card-main mb-6">
      <h1 class="v2-text-h2 mb-3">
        {{ $mylist->title }}
      </h1>
      <div>作成者: {{ $mylist->user->name }}</div>
      @if ($mylist->note)
        <pre class="v2-text-body v2-text-sub whitespace-pre-wrap mb-3">
{{ $mylist->note }}</pre
        >
      @endif
    </div>
    <div id="app-public-mylist" data-mylist-slug="{{ $mylist->slug }}">
      読み込み中...
    </div>
  </div>
@endsection
