<h2 class="section-title">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="articles bg-white">
        @foreach ($articles as $article)
        <article class="pb-2">
            <div class="h-100 d-flex flex-column justify-content-between">
                <div>
                    <a
                        href="{{ route('articles.show', $article->slug)}}"
                        class="thumbnail"
                        style="background-image: url({{$article->thumbnail_url}})"
                        title="{{$article->title}}"
                    >
                    </a>
                    <a
                        href="{{ route('articles.show', $article->slug)}}"
                        title="{{$article->title}}"
                        itemprop="url">
                        <h3 class="m-1 title" itemprop="name">{{ $article->title }}</h3>
                    </a>
                </div>
                <div class="px-1 text-secondary">
                    <div>
                        By <a class="text-secondary" href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a>
                    </div>
                    <div>
                        @include('parts.category-list', ['categories' => $article->category_addons->splice(0, 3)])
                    </div>
                </div>
            </div>
        </article>
    @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
