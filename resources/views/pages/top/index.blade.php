@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <svg aria-hidden="true" class="absolute top-0 left-[max(50%,25rem)] h-256 w-512 -translate-x-1/2 mask-[radial-gradient(64rem_64rem_at_top,white,transparent)] stroke-gray-200">
            <defs>
                <pattern id="e813992c-7d03-4cc4-a2bd-151760b470a0" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
                    <path d="M100 200V.5M.5 .5H200" fill="none" />
                </pattern>
            </defs>
            <svg x="50%" y="-1" class="overflow-visible fill-gray-50">
                <path d="M-100.5 0h201v201h-201Z M699.5 0h201v201h-201Z M499.5 400h201v201h-201Z M-300.5 600h201v201h-201Z" stroke-width="0" />
            </svg>
            <rect width="100%" height="100%" fill="url(#e813992c-7d03-4cc4-a2bd-151760b470a0)" stroke-width="0" />
        </svg>
    </div>
    <div class="mx-auto grid gap-y-16 lg:mx-0 lg:max-w-none lg:gap-y-10">
        <div class="lg:mx-auto lg:grid lg:w-full">
            <div class="lg:pr-4">
                <h1 class="title-xl2">{{ config('app.name') }}</h1>
                <p class="mt-6 text-xl/8 text-gray-700">
                    Simutrans（シムトランス）のaddon（アドオン）を気軽に投稿・紹介できるポータルサイトです。<br />
                    投稿だけでなく、様々なWikiや個人サイト、アップローダーに掲載・投稿されているアドオン紹介記事も掲載できます。<br />
                </p>
            </div>
        </div>
        <div class="lg:mx-auto lg:grid lg:w-full">
            <div class="lg:pr-4">
                <div class=" text-base/7 text-gray-600 ">
                    <h2 class="title-md">お知らせ</h2>
                    @foreach($announces as $article)
                    @include('components.partials.announce-one-liner', ['article' => $article])
                    @endforeach
                    <p class="mt-3">
                        <a href="{{ route('announces') }}" class="font-semibold">一覧<span aria-hidden="true">→</span></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="lg:mx-auto lg:grid lg:w-full">
            <div class="lg:pr-4">
                <div class=" text-base/7 text-gray-600 ">
                    <h2 class="title-md">アドオン関連サイト</h2>
                    <p class="mt-3">
                        @include('components.ui.link-external', ['url' => 'https://forum.simutrans.com/', 'title' => 'International Simutrans Forum'])
                    </p>
                    <p class="mt-3">
                        @include('components.ui.link-external', ['url' => 'https://japanese.simutrans.com/index.php', 'title' => 'Simutrans日本語化･解説'])
                    </p>
                    <p class="mt-3">
                        @include('components.ui.link-external', ['url' => 'https://wikiwiki.jp/twitrans/', 'title' => 'Simutrans的な実験室'])
                    </p>
                    <p class="mt-3">
                        @include('components.ui.link-external', ['url' => 'https://cross-search.128-bit.net/', 'title' => 'Simutrans 横断検索'])
                    </p>
                    <p class="mt-3">
                        @include('components.ui.link-external', ['url' => route('redirect', ['name' => 'simutrans-interact-meeting']), 'title' => 'シムトランス交流会議'])
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
