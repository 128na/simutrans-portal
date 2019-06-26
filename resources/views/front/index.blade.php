@extends('layouts.app')

@section('title', __('message.top'))

@section('content')
    <section class="mb-4">
        <h2 class="border-bottom">{{ __('message.latest') }}</h2>
        @foreach ($articles['latest'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
        <h6><a href="{{ route('addons.index') }}">{{ __('message.show-all-addons') }}</a></h6>
    </section>
    <section class="mb-4">
        <h2 class="border-bottom">{{ __('message.random') }}</h2>
        @foreach ($articles['random'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
    </section>
    <section class="mb-4">
        <h2 class="border-bottom">{{ __('message.pages') }}</h2>
        @foreach ($articles['pages'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
        <h6><a href="{{ route('pages.index') }}">{{ __('message.show-all-pages') }}</a></h6>
    </section>
    <section class="mb-4">
        <h2 class="border-bottom">{{ __('message.announces') }}</h2>
        @foreach ($articles['announces'] as $article)
            @include('front.articles.list-item', ['article' => $article])
        @endforeach
        <h6><a href="{{ route('announces.index') }}">{{ __('message.show-all-announces') }}</a></h6>
    </section>
@endsection
