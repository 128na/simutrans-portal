@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)
@section('card-type', $article->has_thumbnail ? 'summary_large_image' : 'summary')
@section('meta-description', $article->meta_description)
@section('meta-image', $article->thumbnail_url)

@section('content')
    <article class="{{$article->post_type}}">
        @if ($article->has_thumbnail)
            <img src="{{ $article->thumbnail_url }}" class="img-fluid thumbnail mb-4 shadow-sm">
        @endif
        <h1 class="title mb-3">{{$article->title}}</h1>
        @includeIf("front.articles.parts.{$article->post_type}", ['article' => $article])

        <footer class="border-top pt-2">
            <div>
                @lang('Publisher'): <a href="{{route('user', $article->user)}}">{{ $article->user->name }}</a><br>
                @lang('Created at'): <span>{{ $article->created_at->formatLocalized(__('%m-%d-%Y %k:%M:%S')) }}</span>,
                @lang('Updated at'): <span>{{ $article->updated_at->formatLocalized(__('%m-%d-%Y %k:%M:%S')) }}</span>
            </div>
            @can('update', $article)
            <div>
                @lang('Page Views') : <span>{{ $article->totalViewCount->count ?? 'N/A' }}</span>,
                @lang('Conversions') : <span>{{ $article->totalConversionCount->count ?? 'N/A' }}</span>
                <a href="{{ route('mypage.index', ["#/edit/{$article->id}"]) }}" class="text-primary">@lang('Edit')</a>
            </div>
            @endcan
        </footer>
    </article>
    <script type="application/ld+json">
        @json($schemas)
    </script>
@endsection
