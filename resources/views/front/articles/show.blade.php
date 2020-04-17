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
        @includeWhen($article->contents->isAddonIntroductionContent(), 'front.articles.parts.addon-introduction', ['article' => $article])
        @includeWhen($article->contents->isAddonPostContent(), 'front.articles.parts.addon-post', ['article' => $article])
        @includeWhen($article->contents->isPageContent(), 'front.articles.parts.page', ['article' => $article])
        @includeWhen($article->contents->isMarkdownContent(), 'front.articles.parts.markdown', ['article' => $article])

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
    @php
        $schemas = [
            [
                '@context' =>'http://schema.org',
                '@type' =>'Article',
                'name' =>$article->title,
                'publisher' => [
                    '@type' =>'Organization',
                    'logo' => [
                        '@type' =>'ImageObject',
                        'url' => $article->user->profile->avatar_url
                    ],
                    'name' => $article->user->name,
                ],
                'author' => [
                    '@type' =>'Person',
                    'name' => $article->contents->author,
                ],
                'datePublished' => $article->created_at,
                'dateModified' => $article->updated_at,
                'image' => $article->has_thumbnail ? $article->thumbnail_url : null,
                'articleBody' => $article->meta_description,
                'headline' => mb_strimwidth($article->meta_description, 0, 55),
                'mainEntityOfPage' => route('articles.show', $article->slug),
            ],
            [
                '@context'=> 'http://schema.org',
                '@type'=> 'SiteNavigationElement',
                'name'=> __("post_types.{$article->post_type}"),
                'url'=> route('addons.index'),
            ]
        ];
        foreach ($article->categories as $category) {
            $schemas[] = [
                '@context'=> 'http://schema.org',
                '@type'=> 'SiteNavigationElement',
                'name'=> __("category.{$category->type}.{$category->slug}"),
                'url'=> route('category', [$category->type, $category->slug]),
            ];
        }
        foreach ($article->tags as $tag) {
            $schemas[] = [
                '@context'=> 'http://schema.org',
                '@type'=> 'SiteNavigationElement',
                'name'=> $tag->name,
                'url'=> route('tag', $tag),
            ];
        }
    @endphp
    <script type="application/ld+json">
        @json($schemas)
    </script>
@endsection
