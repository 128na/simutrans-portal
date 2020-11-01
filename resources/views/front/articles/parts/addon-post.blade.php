<dl>
    <dt>@lang('Author') / @lang('Publisher')</dt>
    <dd>
        @if ($article->contents->author)
            {{ $article->contents->author }}</a> /
        @endif
        <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
    </dd>
    <dt>@lang('Categories')</dt>
    <dd>
        @include('parts.category-list', ['categories' => $article->categories,
            'post_type' => $article->post_type, 'route_name' => 'addons.index'])
    </dd>
    @if ($article->tags->isNotEmpty())
        <dt>@lang('Tags')</dt>
        <dd>
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
    @endif
    <dt>@lang('Description')</dt>
    <dd>{{ $article->contents->description }}</dd>
    @if ($article->contents->thanks)
        <dt>@lang('Acknowledgments and Referenced')</dt>
        <dd>{{ $article->contents->thanks }}</dd>
    @endif
    @if ($article->contents->license)
        <dt>@lang('License')</dt>
        <dd>{{ $article->contents->license }}</dd>
    @endif
    <dt class="mb-2">@lang('Download')</dt>
    <dd>
        <a class="btn btn-lg btn-primary" href="{{ route('articles.download', $article) }}">@lang('Click to Download')</a>
    </dd>
</dl>
