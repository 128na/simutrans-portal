<article class="flex flex-col sm:flex-row gap-6 items-start">
    <a href="{{ route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user->id, 'articleSlug' => $article->slug]) }}" class="flex-shrink-0">
        <img class="w-full sm:w-80 h-45 object-cover rounded-lg shadow-lg" src="{{ $article->thumbnailUrl }}" alt="">
    </a>

    <div class="flex flex-col justify-between flex-1">
        <div>
            <time datetime="{{$article->published_at->format('Y/m/d')}}" class="text-sm text-gray-500">
                {{$article->published_at->format('Y/m/d')}}
            </time>
            <h3 class="mt-2 text-xl font-semibold text-gray-900 hover:text-gray-600">
                <a href="{{ route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user->id, 'articleSlug' => $article->slug]) }}">
                    {{$article->title}}
                </a>
            </h3>
            <p class="mt-3 text-sm text-gray-600 line-clamp-3">
                {{$article->metaDescription}}
            </p>
        </div>
        <div class="text-xs mt-2 flex flex-wrap gap-2">
            @include('v2.parts.categories', ['categories' => $article->categories])
            @include('v2.parts.tags', ['tags' => $article->tags])
        </div>

        <div class="mt-4 flex items-center gap-x-3">
            @include('v2.parts.user-profile', ['user' => $article->user])
        </div>
    </div>
</article>
