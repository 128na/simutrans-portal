<p class="mt-3">
    <span class="text-sm text-gray-500 mr-1">
        {{$article->published_at->format('Y/m/d')}}
    </span>
    @include('v2.parts.link', ['url' => route('articles.show', ['userIdOrNickname' => $article->user_nickname ?? $article->user_id, 'articleSlug' => $article->slug]), 'title' => $article->title])
</p>
