<div class="mx-auto max-w-7xl p-6 lg:px-8">
    @include('v2.search.options')
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        @foreach($articles as $article)
        @include('v2.parts.addon', ['article' => $article])
        @endforeach
    </div>
    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        {{ $articles->links() }}
    </div>
</div>
