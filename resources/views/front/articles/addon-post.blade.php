<div class="article detail">
    @if ($article->has_thumbnail)
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    @endif
    <dl class="mx-1 mt-2">
        <dt>{{ __('message.article.author') }} / {{ __('message.article.publisher') }}</dt>
        <dd class="mx-1 mt-2">
            {{ $article->author }}</a> / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dt>{{ __('message.article.categories') }}</dt>
        <dd class="mx-1 mt-2">
            @include('parts.category-list', ['categories' => $article->categories])
        </dd>
        <dt>{{ __('message.article.tags') }}</dt>
        <dd class="mx-1 mt-2">
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
        <dt>{{ __('message.article.description') }}</dt>
        <dd class="mt-1 ml-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>{{ __('message.article.thanks') }}</dt>
            <dd class="mt-1 ml-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>{{ __('message.article.license') }}</dt>
            <dd class="mt-1 ml-2">{{ $article->license }}</dd>
        @endif
        <dt>{{ __('message.article.download') }}</dt>
        <dd class="mt-1 ml-2"><a class="btn btn-lg btn-primary" href="{{ route('articles.download', $article) }}">{{ __('message.article.download') }}</a></dd>
    </dl>
</div>
