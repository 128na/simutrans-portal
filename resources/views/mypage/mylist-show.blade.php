@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
    <div class="v2-page v2-page-lg">
        {{-- ナビゲーション --}}
        <div class="flex items-center gap-3 mb-6">
            <x-ui.link url="/mypage/mylists" title="← マイリスト一覧へ" />
            @if($mylist->is_public && $mylist->slug)
                <x-ui.link url="/mylist/{{ $mylist->slug }}" title="公開ページを表示" />
            @endif
        </div>

        {{-- ヘッダー情報 --}}
        <div class="v2-card v2-card-main mb-6">
            <h1 class="v2-text-h2 mb-3">
                <span class="v2-badge {{ $mylist->is_public ? 'v2-badge-success' : 'v2-badge-sub' }}">
                    {{ $mylist->is_public ? '公開' : '非公開' }}
                </span>
                {{ $mylist->title }}
            </h1>
            @if($mylist->note)
                <pre class="v2-text-body v2-text-sub whitespace-pre-wrap mb-3">{{ $mylist->note }}</pre>
            @endif
        </div>
        <div id="app-mylist-detail" data-mylist-id="{{ $mylist->id }}">読み込み中...</div>
    </div>
@endsection
