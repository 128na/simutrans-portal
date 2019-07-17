@if ($article->post_type === 'page')
<div class="mb-3 article-box">
    <h5><a href="{{ route('articles.show', $article->slug)}}">{{ $article->title }}</a></h5>
    <small>{{ __('Last updated: :date', ['date' => $article->updated_at]) }}</small>
</div>
@else
<div class="mb-3 article-box">
    <div class="my-1">
        <h5>
            <a href="{{ route('articles.show', $article->slug)}}">{{ $article->title }}</a>
        </h5>
        <div class="img-full-box mb-1">
            <a href="{{ route('articles.show', $article->slug)}}">
                <img data-src="{{ $article->thumbnail_url }}" class="img-fluid lazy">
            </a>
        </div>
        <div>
            <small class="mr-1">by</small><a href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a>
        </div>
        <div>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->post_type, 'route_name' => 'addons.index'])
            @include('parts.tag-list', ['tags' => $article->tags])
        </div>
        <small>{{ __('Last updated: :date', ['date' => $article->updated_at]) }}</small>
    </div>
</div>
@endif
