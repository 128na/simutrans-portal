@extends('layouts.app')

@section('title', $title ?? 'Articles')

@section('content')
    {!! e($articles->links()) !!}

    <div class="list">
        @forelse ($articles as $article)
            @include('front.articles.list-item', ['article' => $article])
        @empty
            <p>{{ __('message.no-article')}}</p>
        @endforelse
    </div>

    {!! e($articles->links()) !!}
@endsection
