@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)
@section('card-type', $article->has_thumbnail ? 'summary_large_image' : 'summary')
@section('meta-description', $article->meta_description)
@section('meta-image', $article->thumbnail_url)

@section('content')
    <article class="{{ $article->post_type }}">
        <h3 class="title border-bottom mb-4">
            {{ $article->title }}
        </h3>
        @if ($article->has_thumbnail)
            <img src="{{ $article->thumbnail_url }}" class="img-fluid thumbnail mb-4 shadow-sm">
        @endif
        @includeIf("front.articles.parts.{$article->post_type}", ['article' => $article])

        <footer class="border-top pt-2">
            <div>
                投稿者: <a href="{{ route('user', $article->user) }}">{{ $article->user->name }}</a><br>
                投稿日時: <span>{{ $article->created_at->format('Y/m/d H:i') }}</span>,
                最終更新: <span>{{ $article->updated_at->format('Y/m/d H:i') }}</span>
            </div>
        </footer>
    </article>
    <script type="application/ld+json">
        @json($schemas)
    </script>
@endsection
