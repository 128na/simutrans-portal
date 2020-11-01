@extends('layouts.front')

@section('id', 'listing')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}

    <section class="mb-4 list">
        @component('components.articles', ['articles' => $articles, 'hide_detail' => $hide_detail ?? false])
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}

    @isset($schemas)
        <script type="application/ld+json">
            @json($schemas)
        </script>
    @endisset

@endsection
