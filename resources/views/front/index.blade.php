@extends('layouts.front')

@section('title', __('Top'))
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    <section class="mb-4 list">
        <h2 class="border-bottom">{{ __('Announces') }}</h2>
        @unless (empty($articles['pages']))
            @foreach ($articles['announces'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('announces.index') }}">{{ __('Show all announces.') }}</a></h6>
        @else
            <p>{{ __('No article exists.')}}</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">{{ __('Latest Addons') }}</h2>
        @unless (empty($articles['latest']))
            @foreach ($articles['latest'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6>
                <a href="{{ route('addons.index') }}">{{ __('Show all addons.') }}</a>
            </h6>
        @else
            <p>{{ __('No article exists.')}}</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">{{ __('Access Ranking') }}</h2>
        @unless (empty($articles['ranking']))
            @foreach ($articles['ranking'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('addons.ranking') }}">{{ __('Show ranking addons.') }}</a></h6>
        @else
            <p>{{ __('No article exists.')}}</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">{{ __('Pages') }}</h2>
        @unless (empty($articles['pages']))
            @foreach ($articles['pages'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('pages.index') }}">{{ __('Show all pages.') }}</a></h6>
        @else
            <p>{{ __('No article exists.')}}</p>
        @endunless
    </section>

@endsection
