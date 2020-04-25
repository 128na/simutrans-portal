<dl>
    <dt>@lang('Author') / @lang('Publisher')</dt>
    <dd>
        {{ $article->contents->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
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
    <dd>{{ $article->contents->description }}</dd>
    @if ($article->contents->thanks)
        <dt>@lang('Acknowledgments and Referenced')</dt>
        <dd>{{ $article->contents->thanks }}</dd>
    @endif
    @if ($article->contents->license)
        <dt>@lang('License')</dt>
        <dd>{{ $article->contents->license }}</dd>
    @endif
    @if ($article->contents->agreement)
        <dt>@lang('Agreement')</dt>
        <dd>@lang('This article is published by author\'s permission or by author himself.')</dd>
    @endif
    <dt>@lang('Link')</dt>
    <dd>
        <a
            href="{{$article->contents->link}}"
            data-url="{{ $article->contents->link }}"
            data-slug="{{ $article->slug }}"
            class="js-click text-primary"
            target="_blank"
            rel="noopener noreferrer"
        >{{ $article->contents->link }}</a>
    </dd>
</dl>
