<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl">お知らせ</h2>
        <p class="mt-2 text-lg/8 text-gray-600">記事一覧</p>
    </div>
    <div class="mx-auto mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        @foreach($articles as $article)
        @include('v2.parts.addon', ['article' => $article])
        @endforeach
    </div>
</div>
