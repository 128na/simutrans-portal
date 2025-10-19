<p class="mt-3">
    <span class="text-sm text-gray-500">
        {{$article->published_at->format('Y/m/d')}}
    </span>
    <a class="mr-1 underline decoration-gray-400" href="{{ route('articles.show', ['userIdOrNickname' => $article->user_nickname ?? $article->user_id, 'articleSlug' => $article->slug]) }}" class="hover:underline">
        {{$article->title}}
    </a>
</p>
