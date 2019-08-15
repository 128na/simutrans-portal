@extends('layouts.front')

@section('title', $article->title)
@section('meta-description', $article->meta_description)
@section('meta-image', $article->thumbnail_url)

@section('content')
    @include('front.articles.parts.'.$article->post_type, ['article' => $article])
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
