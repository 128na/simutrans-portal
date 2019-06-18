@extends('layouts.app')

@section('title', $article->title)

@section('content')
    @include('front.articles.'.$article->category_post->slug, ['article' => $article])
@endsection
