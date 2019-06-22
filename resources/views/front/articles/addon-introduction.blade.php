<div class="article detail">
    @if ($article->has_thumbnail)
        <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
    @endif
    <dl class="mx-1 mt-2">
        <dt>{{ __('article.author') }} / {{ __('article.publisher') }}</dt>
        <dd class="mx-1 mt-2">
            {{ $article->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dt>{{ __('article.categories') }}</dt>
        <dd class="mx-1 mt-2">
            @include('parts.category-list', ['categories' => $article->categories])
        </dd>
        <dt>{{ __('article.tags') }}</dt>
        <dd class="mx-1 mt-2">
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
        <dt>{{ __('article.description') }}</dt>
        <dd class="mx-1 mt-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>{{ __('article.thanks') }}</dt>
            <dd class="mx-1 mt-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>{{ __('article.license') }}</dt>
            <dd class="mx-1 mt-2">{{ $article->license }}</dd>
        @endif
        @if ($article->agreement)
            <dt>{{ __('article.agreement') }}</dt>
            <dd class="mx-1 mt-2">{{ __('article.ageement-message') }}</dd>
        @endif
        <dt>{{ __('article.link') }}</dt>
        <dd class="mx-1 mt-2">
            <a href="#" data-url="{{ $article->link }}" data-slug="{{ $article->slug }}" class="js-click">{{ $article->link }}</a>
        </dd>
    </dl>
</div>
