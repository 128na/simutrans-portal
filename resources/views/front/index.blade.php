@extends('layouts.app')

@section('title', 'Top')

@section('content')
    <div class="list">
        <h2>Latest</h2>
        @foreach ($articles['latest'] as $article)
            @include('front.article.list-item', ['article' => $article])
        @endforeach

        <h2>Random</h2>
        @foreach ($articles['random'] as $article)
            @include('front.article.list-item', ['article' => $article])
        @endforeach
    </div>
@endsection
