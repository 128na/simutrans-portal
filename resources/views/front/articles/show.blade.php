@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)
@section('card-type', $article->has_thumbnail ? 'summary_large_image' : 'summary')
@section('meta-description', $article->meta_description)
@section('meta-image', $article->thumbnail_url)

@section('content')
    <script>
        window.article = {{ Js::from($articleResource) }};
        window.attachments = {{ Js::from($attachmentResource) }};
    </script>
    <script src="{{ asset(mix('js/front_spa.js')) }}" defer></script>
    <div id="app"></div>
    <script type="application/ld+json">
        @json($schemas)
    </script>
@endsection
