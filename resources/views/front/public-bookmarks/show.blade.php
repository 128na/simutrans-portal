@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)

@section('content')
    <article>
        <h3>{{ $item->title }}</h3>
        <div class="mb-4">
            {{ $item->description }}
        </div>
        @auth
            <div id="app">
                <page-sub-title>投稿記事エクスポート</page-sub-title>
                <page-description>
                    ブックマーク内の記事を一括でダウンロードできます。<br />
                    記事数が多いとファイルの生成には数分かかることがあります。
                    <bulk-zip-downloader target_type="public_bookmark" target_id="{{ $item->uuid }}" class="mb-3" />
                </page-description>
            </div>
        @endauth
        <div>
            <h5>ブックマークアイテム一覧</h5>
            <ul class="list-style-none">
                @forelse ($item->bookmarkItems as $bookmarkItem)
                    <li class="mb-2">
                        @includeIf("front.public-bookmarks.parts.{$bookmarkItem->type_name}", [
                        'item' =>$bookmarkItem->bookmarkItemable])
                        <div class="ml-4">
                            {{ $bookmarkItem->memo }}
                        </div>
                    </li>
                @empty
                    <li>空のブックマークです</li>
                @endforelse
            </ul>

        </div>
    </article>
@endsection
