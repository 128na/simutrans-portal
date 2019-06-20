<div class="article detail">
    @if ($article->has_thumbnail)
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    @endif
    <dl class="mx-1 mt-2">
        <dt>Author / Publisher</dt>
        <dd class="mx-1 mt-2">
            {{ $article->author }}</a> / <a href="#" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dt>Categories</dt>
        <dd class="mx-1 mt-2">
            @include('parts.category-list', ['categories' => $article->categories])
        </dd>
        <dt>Tags</dt>
        <dd class="mx-1 mt-2">
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
        <dt>Description</dt>
        <dd class="mt-1 ml-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>Thanks</dt>
            <dd class="mt-1 ml-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>License</dt>
            <dd class="mt-1 ml-2">{{ $article->license }}</dd>
        @endif
        <dt>Download</dt>
        <dd class="mt-1 ml-2"><a class="btn btn-lg btn-primary" href="{{ route('articles.download', $article) }}">Download</a></dd>
    </dl>
</div>
