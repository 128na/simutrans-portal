<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-3">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <div class="page-contents markdown">
        @markdown($article->contents->data)
    </div>

    <dl class="mx-1 mt-2">
        <dt>@lang('Categories')</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->isAnnounce() ? null : $article->post_type, 'route_name' => 'pages.index'])
        </dd>
    </dl>
</div>
