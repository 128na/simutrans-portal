@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <svg aria-hidden="true" class="absolute top-0 left-[max(50%,25rem)] h-256 w-512 -translate-x-1/2 mask-[radial-gradient(64rem_64rem_at_top,white,transparent)] stroke-c-sub/10">
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
    <div class="v2-page-content-area-lg">
        <div class="space-y-8">
            <div>
                <h1 class="v2-text-h1 mb-6">{{ config('app.name') }}</h1>
                <p class="text-xl/8 text-c-sub">
                    Simutrans（シムトランス）のaddon（アドオン）を気軽に投稿・紹介できるポータルサイトです。<br />
                    投稿だけでなく、様々なWikiや個人サイト、アップローダーに掲載・投稿されているアドオン紹介記事も掲載できます。<br />
                </p>
            </div>
            <div class="grid gap-10">
                <section class="space-y-8">
                    <h3 class="v2-text-h4">@include('components.ui.link', ['url' => route('latest'), 'title' => 'アドオン新着一覧'])</h3>

                    <div>
                        <h3 class="v2-text-h4 mb-4">@include('components.ui.link', ['url' => route('announces'), 'title' => 'お知らせ一覧'])</h3>
                        <div class="gap-2 flex flex-col">
                            @foreach($announces as $article)
                            @include('components.partials.one-liner', ['article' => $article])
                            @endforeach
                        </div>
                    </div>
                </section>
                <section class="space-y-4">
                    <h3 class="v2-text-h4">関連サイトのリンク</h3>
                    <div class="gap-2 grid">
                        @include('components.ui.link', ['url' => 'https://forum.simutrans.com/', 'title' => 'International Simutrans Forum'])
                        @include('components.ui.link', ['url' => 'https://japanese.simutrans.com/index.php', 'title' => 'Simutrans 日本語化･解説'])
                        @include('components.ui.link', ['url' => 'https://wikiwiki.jp/twitrans/', 'title' => 'Simutrans 的な実験室'])
                        @include('components.ui.link', ['url' => 'https://cross-search.128-bit.net/', 'title' => 'Simutrans 横断検索'])
                        @include('components.ui.link', ['url' => route('redirect', ['name' => 'simutrans-interact-meeting']), 'title' => 'シムトランス交流会議'])
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

@endsection
