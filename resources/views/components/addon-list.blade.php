<h2 class="border-bottom">{{ $slot }}</h2>
@unless (empty($articles))
    <div class="d-flex flex-wrap addon-list">
        @foreach ($articles as $article)
            <div class="col-md-4 col-sm-6 px-0 px-sm-1 py-1 highlightable">
                <h6>
                    <a href="{{ route('articles.show', $article->slug)}}">{{ $article->title }}</a>
                </h6>
                <div class="img-full-box mb-1 text-center">
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
                <small>{{ $article->updated_at }}</small>
            </div>
    @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endunless
