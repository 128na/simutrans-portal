<div class="article detail">
    @if ($article->has_thumbnail)
        <div class="mb-4">
            <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
        </div>
    @endif
    @foreach ($article->getContents('sections', []) as $section)
        <div class="mb-4">
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
        </div>
    @endforeach

    <dl class="mx-1 mt-2">
        <dt>{{ __('article.categories') }}</dt>
        <dd class="mx-1 mt-2">
            @include('parts.category-list', ['categories' => $article->categories])
        </dd>
    </dl>
</div>
