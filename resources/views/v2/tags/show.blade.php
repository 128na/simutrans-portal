@extends('v2.parts.layout')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl">タグ「{{$tag->name}}」の記事</h2>
        <pre class="mt-2 text-lg/8 text-gray-600 whitespace-pre-wrap break-all">{{$tag->description}}</pre>
        <div class="mt-2">
            @include('v2.parts.link', ['url' => route('search', ['tagIds' => [$tag->id]]), 'title' => 'さらに検索条件を追加する'])
        </div>
    </div>
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        @forelse($articles as $article)
        @include('v2.parts.addon', ['article' => $article])
        @empty
        <div class="text-gray-500">記事が見つかりませんでした。</div>
        @endforelse
    </div>
    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->links() }}
    </div>
</div>

@endsection
