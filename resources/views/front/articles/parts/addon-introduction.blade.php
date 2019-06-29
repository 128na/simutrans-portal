<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-2">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <dl class="mx-1 mt-2">
        <dt>{{ __('article.author') }} / {{ __('article.publisher') }}</dt>
        <dd>
            {{ $article->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dt>{{ __('article.categories') }}</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->post_type, 'route_name' => 'addons.index'])
        </dd>
        <dt>{{ __('article.tags') }}</dt>
        <dd>
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
        <dt>{{ __('article.description') }}</dt>
        <dd>{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>{{ __('article.thanks') }}</dt>
            <dd>{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>{{ __('article.license') }}</dt>
            <dd>{{ $article->license }}</dd>
        @endif
        @if ($article->agreement)
            <dt>{{ __('article.agreement') }}</dt>
            <dd>{{ __('article.ageement-message') }}</dd>
        @endif
        <dt>{{ __('article.link') }}</dt>
        <dd>
            <a href="#" data-url="{{ $article->link }}" data-slug="{{ $article->slug }}" class="js-click">{{ $article->link }}</a>
        </dd>
    </dl>
</div>
