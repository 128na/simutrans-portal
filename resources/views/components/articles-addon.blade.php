<h2 class="section-title">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="articles bg-white">
        @foreach ($articles as $article)
        <article class="pb-2">
            <a
                href="{{ route('articles.show', $article->slug)}}"
                class="thumbnail popover-thumbnail"
                style="background-image: url({{$article->thumbnail_url}})"
                data-src="{{$article->thumbnail_url}}"
            >
            </a>
            <a
                href="{{ route('articles.show', $article->slug)}}"
                title="{{$article->title}}"
                itemprop="url">
                <h3 class="m-1 title" itemprop="name">{{ $article->title }}</h3>
            </a>
            <div class="px-1">
                <a class="text-secondary" href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a>
            </div>
            <div class="px-1">
                @include('parts.category-list', ['categories' => $article->category_addons->splice(0, 3)])
            </div>
        </article>
    @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
