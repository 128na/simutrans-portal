@extends('layouts.front')

@section('id', 'top')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    <section class="mb-4">
        @component('components.articles-addon', ['articles' => $ranking])
            <a class="no-underline" href="{{ route('addons.ranking') }}">@lang('Access Ranking')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    @foreach ($latest as $pak => $latest_articles)
        <section class="mb-4">
            @component('components.articles-addon', ['articles' => $latest_articles])
                <a class="no-underline" href="{{ route('category', ['pak', $pak]) }}">@lang(':pak Latest Addons', ['pak' => __("category.pak.$pak")])</a>
                @slot('no_item')
                    @lang('No article exists.')
                @endslot
            @endcomponent
        </section>
    @endforeach

    <section class="mb-4">
        @component('components.articles-page', ['articles' => $pages])
            <a class="no-underline" href="{{ route('pages.index') }}">@lang('Pages')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    <section class="mb-4">
        @component('components.articles-page', ['articles' => $announces])
            <a class="no-underline" href="{{ route('announces.index') }}">@lang('Announces')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

@endsection
