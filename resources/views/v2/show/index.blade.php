@extends('v2.parts.layout')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8 mb-32">
    <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl mb-8 break-words">{{$article->title}}</h2>
    <div class="text-sm flex flex-wrap gap-2">
        @include('v2.parts.categories', ['categories' => $article->categories])
        @include('v2.parts.tags', ['tags' => $article->tags])
    </div>
    <img src="{{ $article->thumbnailUrl }}" alt="" class="mt-6 mb-12 w-max-full rounded-lg shadow-md">
    @include("v2.show.type-{$article->post_type->value}")

    @if($article->articles->isNotEmpty())
    <h4 class="text-xl font-semibold sm:text-xl my-8">関連記事</h4>
    @foreach($article->articles as $a)
    @include('v2.parts.link', ['url' => route('articles.show', ['userIdOrNickname' => $a->user->nickname ?? $a->user->id, 'articleSlug' => $a->slug]), 'title' => $a->title])
    @endforeach
    @endif

    @if($article->relatedArticles->isNotEmpty())
    <h4 class="text-xl font-semibold text-gray-800 sm:text-xl my-8">関連付けられた記事</h4>
    @foreach($article->relatedArticles as $a)
    @include('v2.parts.link', ['url' => route('articles.show', ['userIdOrNickname' => $a->user->nickname ?? $a->user->id, 'articleSlug' => $a->slug]), 'title' => $a->title])
    @endforeach
    @endif

    <h4 class="text-xl font-semibold sm:text-xl my-8">投稿者</h4>
    @include('v2.parts.user-profile', ['user' => $article->user])
</div>

@endsection
