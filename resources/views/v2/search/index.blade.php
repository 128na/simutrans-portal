@extends('v2.parts.layout')

@section('max-w', 'max-w-7xl')
@section('content')
<script id="data-articles" type="application/json">
    @json($articles)

</script>
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    @include('v2.search.options')
    <div id="app-article-list"></div>

    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->withQueryString() }}
    </div>
</div>

@endsection
