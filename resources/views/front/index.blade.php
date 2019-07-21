@extends('layouts.front')

@section('title', __('Top'))
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    <section class="mb-4 list">
        @component('components.page-list', ['articles' => $announces])
            <a href="{{ route('announces.index') }}">@lang('Announces')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    @foreach ($latest as $pak => $latest_articles)
        <section class="mb-4 list">
            @component('components.addon-list', ['articles' => $latest_articles])
                <a href="{{ route('category', ['pak', $pak]) }}">@lang(':pak Latest Addons', ['pak' => __("category.pak.$pak")])</a>
                @slot('no_item')
                    @lang('No article exists.')
                @endslot
            @endcomponent
        </section>
    @endforeach

    <section class="mb-4 list">
        @component('components.addon-list', ['articles' => $ranking])
            <a href="{{ route('addons.ranking') }}">@lang('Access Ranking')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    <section class="mb-4 list">
        @component('components.page-list', ['articles' => $pages])
            <a href="{{ route('pages.index') }}">@lang('Pages')</a>
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>
@endsection
