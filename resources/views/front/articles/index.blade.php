@extends('layouts.app')

@section('title', 'Articles')

@section('content')
    {{ $articles->links() }}

    <div class="list">
        @foreach ($articles as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
    </div>

    {{ $articles->links() }}
@endsection
