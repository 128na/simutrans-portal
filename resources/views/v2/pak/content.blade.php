<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0">
            <h2 class="text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">@lang("category.pak.{$pak}")</h2>
            <p class="mt-2 text-lg/8 text-gray-600">記事一覧</p>
        </div>
        <div class="mx-auto mt-10 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach($articles as $article)
            @include('v2.parts.addon', ['article' => $article])
            @endforeach
        </div>
    </div>
</div>
