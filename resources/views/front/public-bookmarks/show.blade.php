@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)

@section('content')
    <article>
        <h3>{{ $item->title }}</h3>
        <div class="mb-4">
            {{ $item->description }}
        </div>
        <div>
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
