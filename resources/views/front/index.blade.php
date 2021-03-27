@extends('layouts.front')

@section('id', 'top')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/' . config('app.meta-image')))

@section('content')
    @foreach ($latest as $pak => $latest_articles)
        <section class="mb-5">
            @component('components.articles', ['articles' => $latest_articles])
                <a href="{{ route('category', ['pak', $pak]) }}">@lang("category.pak.$pak")の新着アドオン</a>
                @slot('no_item')
                    記事がありません。
                @endslot
            @endcomponent
        </section>
    @endforeach

    <section class="mb-5">
        @component('components.articles', ['articles' => $ranking])
            <a href="{{ route('addons.ranking') }}">アクセスランキング</a>
            @slot('no_item')
                記事がありません。
            @endslot
        @endcomponent
    </section>

    <section class="mb-5">
        @component('components.articles', ['articles' => $pages, 'hide_detail' => true])
            <a href="{{ route('pages.index') }}">一般記事</a>
            @slot('no_item')
                記事がありません。
            @endslot
        @endcomponent
    </section>

    <section class="mb-5">
        @component('components.articles', ['articles' => $announces, 'hide_detail' => true])
            <a href="{{ route('announces.index') }}">お知らせ</a>
            @slot('no_item')
                記事がありません。
            @endslot
        @endcomponent
    </section>

    <script type="application/ld+json">
        @json($schemas)

    </script>

@endsection
