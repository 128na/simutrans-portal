@extends('layouts.app')

@section('title', __('message.top'))

@section('content')
    <div class="list">
        <h2>{{ __('message.pages') }}</h2>
        @foreach ($articles['pages'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach

        <p><a href="{{ route('pages.index') }}">{{ __('message.show-all-pages') }}</a>

            <h2>{{ __('message.latest') }}</h2>
        @foreach ($articles['latest'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach

        <p><a href="{{ route('addons.index') }}">{{ __('message.show-all-addons') }}</a>

        <h2>{{ __('message.random') }}</h2>
        @foreach ($articles['random'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
    </div>
@endsection
