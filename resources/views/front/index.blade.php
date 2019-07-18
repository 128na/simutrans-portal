@extends('layouts.front')

@section('title', __('Top'))
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    <section class="mb-4 list">
        <h2 class="border-bottom">@lang('Announces')</h2>
        @unless (empty($articles['pages']))
            @foreach ($articles['announces'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('announces.index') }}">@lang('Show all announces.')</a></h6>
        @else
            <p>@lang('No article exists.')</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">@lang('Latest Addons')</h2>
        @unless (empty($articles['latest']))
            @foreach ($articles['latest'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6>
                <a href="{{ route('addons.index') }}">@lang('Show all addons.')</a>
            </h6>
        @else
            <p>@lang('No article exists.')</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">@lang('Access Ranking')</h2>
        @unless (empty($articles['ranking']))
            @foreach ($articles['ranking'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('addons.ranking') }}">@lang('Show ranking addons.')</a></h6>
        @else
            <p>@lang('No article exists.')</p>
        @endunless
    </section>

    <section class="mb-4 list">
        <h2 class="border-bottom">@lang('Pages')</h2>
        @unless (empty($articles['pages']))
            @foreach ($articles['pages'] as $article)
                @include('front.articles.parts.list-item', ['article' => $article])
            @endforeach
            <h6><a href="{{ route('pages.index') }}">@lang('Show all pages.')</a></h6>
        @else
            <p>@lang('No article exists.')</p>
        @endunless
    </section>

@endsection
