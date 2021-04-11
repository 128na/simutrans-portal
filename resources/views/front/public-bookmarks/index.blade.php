@extends('layouts.front')

@section('id', 'listing')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/' . config('app.meta-image')))

@section('content')
    @includeWhen(isset($advancedSearch), 'parts.advanced-search')

    {!! e($items->onEachSide(1)->links('vendor.pagination.default')) !!}

    <section class="mb-4 list">
        @component('components.bookmarks', ['items' => $items])
            @slot('no_item')
                公開ブックマークがありません。
            @endslot
        @endcomponent
    </section>

    {!! e($items->onEachSide(1)->links('vendor.pagination.default')) !!}


@endsection
