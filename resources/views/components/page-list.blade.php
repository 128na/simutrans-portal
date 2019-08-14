<h2 class="border-bottom">{{ $slot }}</h2>
@if ($articles->isNotEmpty())
    <div class="mb-1">
        @foreach ($articles as $article)
            <div>
                {{ $article->updated_at->formatLocalized(__('%m-%d-%Y')) }}
                <a href="{{ route('articles.show', $article->slug)}}" itemprop="url"><span itemprop="name">{{ $article->title }}</span></a>
                <small class="mr-1">by</small><a href="{{ route('user', [$article->user]) }}">{{ $article->user->name}}</a>
            </div>
        @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
