@extends('layouts.app')

@section('title', 'Edit '.$article->title)

@section('content')
    <h1>Edit {{ $article->title}}</h1>
    <form method="POST" action="{{ route('mypage.articles.update', $article) }}" enctype="multipart/form-data">
        @include('parts.form-article')
    </form>
@endsection
