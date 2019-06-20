<div class="mb-4 article-box">
    <a href="{{ route('articles.show', $article->slug)}}">
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    </a>
    <div class="ml-2 my-1">
        <h5>
            <a href="{{ route('articles.show', $article->slug)}}">{{ $article->title }}</a>
        </h5>
        <div>
            <small>by</small> <a href="#">{{ $article->user->name}}</a>
        </div>
        <div>
            @include('parts.category-list', ['categories' => $article->categories])
        </div>
        <div>
            @include('parts.tag-list', ['tags' => $article->tags])
        </div>
        <div>Last updated: {{ $article->updated_at }}</div>
    </div>
</div>
