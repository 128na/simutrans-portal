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
        <div class="lg:mx-auto lg:grid lg:w-full lg:px-8">
            <div class="lg:pr-4">
                <h1 class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">{{ config('app.name') }}</h1>
                <p class="mt-6 text-xl/8 text-gray-700">
                    Simutrans（シムトランス）のaddon（アドオン）を気軽に投稿・紹介できるポータルサイトです。<br />
                    投稿だけでなく、様々なWikiや個人サイト、アップローダーに掲載・投稿されているアドオン紹介記事も掲載できます。<br />
                </p>
            </div>
        </div>
        <div class="lg:mx-auto lg:grid lg:w-full lg:px-8">
            <div class="lg:pr-4">
                <div class=" text-base/7 text-gray-600 ">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">新着記事</h2>
                    @foreach($latest as $article)
                    @include('v2.parts.article-one-liner', ['article' => $article])
                    @endforeach
                </div>
            </div>
        </div>
        <div class="lg:mx-auto lg:grid lg:w-full lg:px-8">
            <div class="lg:pr-4">
                <div class=" text-base/7 text-gray-600 ">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">お知らせ</h2>
                    @foreach($announces as $article)
                    @include('v2.parts.article-one-liner', ['article' => $article])
                    @endforeach
                    <p class="mt-3">
                        <a href="{{ route('announces') }}" class="font-bold">一覧<span aria-hidden="true">→</span></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
