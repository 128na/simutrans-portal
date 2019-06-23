@extends('layouts.app')

@section('title', $article->title)

@section('content')
    @include('front.articles.'.$article->post_type, ['article' => $article])
@endsection
