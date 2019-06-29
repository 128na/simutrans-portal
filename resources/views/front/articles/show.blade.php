@extends('layouts.app')

@section('title', $article->title)
@section('meta-description', $article->meta_description)
@section('meta-image', $article->thumbnail_url)

@section('content')
    @include('front.articles.parts.'.$article->post_type, ['article' => $article])
@endsection
