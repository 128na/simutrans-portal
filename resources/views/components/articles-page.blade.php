<h2 class="section-title">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="pages bg-white">
        @foreach ($articles as $article)
            <article class="px-1 py-2">
                <a href="{{ route('articles.show', $article->slug)}}" itemprop="url"><span itemprop="name">{{ $article->title }}</span></a>
                <small>
                    At {{ $article->updated_at->formatLocalized(__('%m-%d-%Y')) }},
                    By <a href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a>
                </small>
            </article>
        @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
