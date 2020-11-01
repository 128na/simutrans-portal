<h2 class="section-title">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="pages">
        @foreach ($articles as $article)
            <article class="mb-4">
                <div class="article-header border-bottom mb-1">
                    <a href="{{ route('articles.show', $article->slug)}}">
                        <strong>{{ $article->title }}</strong>
                    </a>
                </div>
                <div class="article-footer">
                    <span class="mr-2">{{ $article->updated_at->formatLocalized(__('%m-%d-%Y')) }}</span>
                    <span class="mr-2"><a href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a></span>
                    <span class="mr-2">
                        @include('parts.category-list', ['categories' => $article->categories])
                    </span>
                </div>
            </article>
        @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
