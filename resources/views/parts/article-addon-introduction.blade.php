<div class="article detail">
    <h1>{{ $article->title }}</h1>
    <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    <dl class="mx-1 mt-2">
        <dt>Author / Publisher</dt>
        <dd class="mx-1 mt-2">
            {{ $article->author }}</a> / <a href="#" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dd class="mx-1 mt-2"><a href="#" rel="author">{{ $article->author }}</a></dd>
        <dt>Categories</dt>
        <dd class="mx-1 mt-2">
            @include('parts.category-list', ['categories' => $article->categories])
        </dd>
        <dt>Description</dt>
        <dd class="mx-1 mt-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>Thanks</dt>
            <dd class="mx-1 mt-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>License</dt>
            <dd class="mx-1 mt-2">{{ $article->license }}</dd>
        @endif
        <dt>Download</dt>
        <dd class="mx-1 mt-2"><a href="{{ $article->link }}" target="_blank" rel="noopener nofollow noreferrer">{{ $article->link }}</a></dd>
    </dl>
</div>
