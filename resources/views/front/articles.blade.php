@extends('layouts.app')

@section('title', $article->title)

@section('content')
    @include('parts.article-'.$article->post_type->slug, ['article' => $article])
@endsection
