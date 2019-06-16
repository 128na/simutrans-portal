<div class="mb-4 article-box">
    <a href="{{ route('articles', $article->slug)}}">
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    </a>
    <div class="ml-2 my-1">
        <h5>
            <a href="{{ route('articles', $article->slug)}}">{{ $article->title }}</a>
        </h5>
        <p>
            <a href="#">{{ $article->author}}</a>
        </p>
        @foreach ($article->categories as $category)
            <a href="#" class="btn btn-sm btn-secondary">{{ $category->name }}</a>
        @endforeach
    </div>
</div>
