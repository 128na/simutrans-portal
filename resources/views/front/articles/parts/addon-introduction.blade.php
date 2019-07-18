<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-2">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <dl class="mx-1 mt-2">
        <dt>@lang('Author') / @lang('Publisher')</dt>
        <dd>
            {{ $article->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
        </dd>
        <dt>@lang('Categories')</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->post_type, 'route_name' => 'addons.index'])
        </dd>
        <dt>@lang('Tags')</dt>
        <dd>
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
        <dt>@lang('Description')</dt>
        <dd>{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>@lang('Acknowledgments and Referenced')</dt>
            <dd>{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>@lang('License')</dt>
            <dd>{{ $article->license }}</dd>
        @endif
        @if ($article->agreement)
            <dt>@lang('Agreement')</dt>
            <dd>@lang('This article is published by author\'s permission or by author himself.')</dd>
        @endif
        <dt>@lang('Link')</dt>
        <dd>
            <a href="#" data-url="{{ $article->link }}" data-slug="{{ $article->slug }}" class="js-click">{{ $article->link }}</a>
        </dd>
    </dl>
</div>
