<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-2">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <div class="page-contents">
    @foreach ($article->getContents('sections', []) as $section)
        <section>
            @switch($section['type'])
                @case('caption')
                    <h3>{{ $section['caption'] }}</h3>
                    @break
                @case('text')
                    <div>{{ $section['text'] }}</div>
                    @break
                @case('image')
                    <img class="img-thumbnail" src="{{ $article->getImageUrl($section['id']) }}">
                    @break
            @endswitch
        </section>
    @endforeach
    </div>

    <dl class="mx-1 mt-2">
        <dt>{{ __('article.categories') }}</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->isAnnounce() ? null : $article->post_type, 'route_name' => 'pages.index'])
        </dd>
    </dl>
</div>
