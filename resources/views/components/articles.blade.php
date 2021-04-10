<h2 class="section-title">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="articles">
        @foreach ($articles as $article)
            <article class="mb-4">
                <div class="article-header border-bottom mb-1">
                    <a href="{{ route('articles.show', $article->slug) }}">
                        <strong>{{ $article->title }}</strong>
                    </a>
                    @auth
                        @include('parts.add-bookmark', [
                        'name' => $article->title,
                        'type' => 'App\Models\Article',
                        'id' => $article->id])
                    @endauth
                </div>
                @unless($hide_detail ?? false)
                    <div class="article-main d-flex mb-1">
                        <a href="{{ route('articles.show', $article->slug) }}">
                            <img src="{{ $article->thumbnail_url }}" loading="lazy">
                        </a>
                        <span class="pl-2 article-description">
                            {{ $article->contents->description ?? '' }}
                        </span>
                    </div>
                @endunless
                <div class="article-footer">
                    <span class="mr-2">{{ $article->updated_at->format('Y/m/d') }}</span>
                    <span class="mr-2"><a
                            href="{{ route('user', [$article->user]) }}">{{ $article->user->name }}</a></span>
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
