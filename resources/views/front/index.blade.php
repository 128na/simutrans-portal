@extends('layouts.app')

@section('title', __('message.top'))

@section('content')
    <div class="list">
        <h2>{{ __('message.latest') }}</h2>
        @foreach ($articles['latest'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach

        <p><a href="{{ route('articles.index') }}">{{ __('message.show-all-articles') }}</a>

        <h2>{{ __('message.random') }}</h2>
        @foreach ($articles['random'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
    </div>
@endsection
