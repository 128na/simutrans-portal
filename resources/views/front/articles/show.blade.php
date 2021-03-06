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
            @auth
                @include('parts.add-bookmark', [
                'name' => $article->title,
                'type' => 'App\Models\Article',
                'id' => $article->id])
            @endauth
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
            @can('update', $article)
                <div>
                    PV: <span>{{ $article->totalViewCount->count ?? 'N/A' }}</span>,
                    CV: <span>{{ $article->totalConversionCount->count ?? 'N/A' }}</span>
                    <a href="{{ route('mypage.index', ["#/edit/{$article->id}"]) }}" class="text-primary">記事を編集</a>
                </div>
            @endcan
        </footer>
    </article>
    <script type="application/ld+json">
        @json($schemas)

    </script>
@endsection
