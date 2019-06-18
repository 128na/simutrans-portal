<div class="mb-4 article-box">
    <a href="{{ route('articles', $article->slug)}}">
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    </a>
    <div class="ml-2 my-1">
        <h5>
            <a href="{{ route('articles', $article->slug)}}">{{ $article->title }}</a>
        </h5>
        <div>
            <small>by</small> <a href="#">{{ $article->user->name}}</a>
        </div>
        <div>
            @include('parts.category-list', ['categories' => $article->categories])
        </div>
        <div>
            <small>post:</small> {{ $article->created_at }}.
            @if ($article->updated_at > $article->created_at)
                <small>updated:</small> {{ $article->updated_at }}.
            @endif
        </div>
    </div>
</div>
