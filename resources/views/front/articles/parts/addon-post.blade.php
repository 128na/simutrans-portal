<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-2">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <dl class="mx-1 mt-2">
        <dt>@lang('Author') / @lang('Publisher')</dt>
        <dd>
            @if ($article->author)
                {{ $article->author }}</a> /
            @endif
            <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
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
        <dd class="mt-1 ml-2">{{ $article->description }}</dd>
        @if ($article->thanks)
            <dt>@lang('Acknowledgments and Referenced')</dt>
            <dd class="mt-1 ml-2">{{ $article->thanks }}</dd>
        @endif
        @if ($article->license)
            <dt>@lang('License')</dt>
            <dd class="mt-1 ml-2">{{ $article->license }}</dd>
        @endif
        <dt>@lang('Download')</dt>
        <dd class="mt-1 ml-2"><a class="btn btn-lg btn-primary" href="{{ route('articles.download', $article) }}">@lang('Click to Download')</a></dd>
    </dl>
</div>
