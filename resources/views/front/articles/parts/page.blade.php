@foreach ($article->contents->sections ?? [] as $section)
    <section>
        @switch($section['type'])
            @case('caption')
                <h2 class="my-2">{{ $section['caption'] }}</h2>
                @break
            @case('text')
                <div class="text">{{ $section['text'] }}</div>
                @break
            @case('url')
                <div class="url">
                    <a href="{{$section['url']}}" class="text-primary" target="_blank" rel="noopener noreferrer">{{ $section['url'] }}</a>
                </div>
                @break
            @case('image')
                <div class="text-center">
                    <img class="img-fluid" src="{{ $article->getImageUrl($section['id']) }}">
                </div>
                @break
        @endswitch
    </section>
@endforeach

@if ($article->categories->isNotEmpty())
    <dl class="mt-4">
        <dt>@lang('Categories')</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->isAnnounce() ? null : $article->post_type, 'route_name' => 'pages.index'])
        </dd>
    </dl>
@endif
