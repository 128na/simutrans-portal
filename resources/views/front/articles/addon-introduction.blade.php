<div class="article detail">
    @if ($article->has_thumbnail)
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    @endif
    <dl class="mx-1 mt-2">
        <dt>{{ __('message.article.author') }} / {{ __('message.article.publisher') }}</dt>
        <dd class="mx-1 mt-2">
            {{ $article->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
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
        <dd class="mx-1 mt-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>{{ __('message.article.thanks') }}</dt>
            <dd class="mx-1 mt-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>{{ __('message.article.license') }}</dt>
            <dd class="mx-1 mt-2">{{ $article->license }}</dd>
        @endif
        @if ($article->agreement)
            <dt>{{ __('message.article.agreement') }}</dt>
            <dd class="mx-1 mt-2">The add-on author has given you permission to introduce add-ons (or the author's own post)</dd>
        @endif
        <dt>{{ __('message.article.link') }}</dt>
        <dd class="mx-1 mt-2">
            <a href="#" data-url="{{ $article->link }}" data-slug="{{ $article->slug }}" class="js-click">{{ $article->link }}</a>
        </dd>
    </dl>
</div>
