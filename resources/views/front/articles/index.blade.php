@extends('layouts.front')

@section('id', 'listing')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}

    <section class="mb-4 list">
        @isset($show_page_component)
            @component('components.articles-page', ['articles' => $articles])
                @slot('no_item')
                    @lang('No article exists.')
                @endslot
            @endcomponent
        @else
            @component('components.articles-addon', ['articles' => $articles])
                @slot('no_item')
                    @lang('No article exists.')
                @endslot
            @endcomponent
        @endisset
    </section>

    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}

    @isset($schemas)
        <script type="application/ld+json">
            @json($schemas)
        </script>
    @endisset

@endsection
