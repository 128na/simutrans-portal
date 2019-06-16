<div class="article detail">
    <h1>{{ $article->title }}</h1>
    <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    <dl class="mx-1 mt-2">
        <dt>Author</dt>
        <dd class="mx-1 mt-2"><a href="#" rel="author">{{ $article->author }}</a></dd>
        <dt>Categories</dt>
        <dd class="mx-1 mt-2">
            @foreach ($article->categories as $category)
                <a href="#" class="btn btn-sm btn-secondary" rel="tag">{{ $category->name }}</a>
            @endforeach
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
