@extends('layouts.app')

@section('title', $article->title)

@section('content')
    @include('parts.article-'.$article->category_post->slug, ['article' => $article])
@endsection
