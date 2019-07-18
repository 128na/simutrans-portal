<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="img-full-box mb-3">
            <img src="{{ $article->thumbnail_url }}" class="img-fluid">
        </div>
    @endif
    <div class="page-contents">
    @foreach ($article->getContents('sections', []) as $section)
        <section class="mb-2">
            @switch($section['type'])
                @case('caption')
                    <h3 class="my-3">{{ $section['caption'] }}</h3>
                    @break
                @case('text')
                    <div class="pl-1">{{ $section['text'] }}</div>
                    @break
                @case('image')
                    <div class="text-center">
                        <img class="img-fluid" src="{{ $article->getImageUrl($section['id']) }}">
                    </div>
                    @break
            @endswitch
        </section>
    @endforeach
    </div>

    <dl class="mx-1 mt-2">
        <dt>@lang('Categories')</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->isAnnounce() ? null : $article->post_type, 'route_name' => 'pages.index'])
        </dd>
    </dl>
</div>
