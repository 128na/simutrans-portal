<p class="truncate">
    <span class="text-sm text-c-sub/70 mr-1">
        {{$article->modified_at ? $article->modified_at->format('Y/m/d') : $article->published_at->format('Y/m/d')}}
    </span>
    @include('components.ui.link', [
        'url' => route('articles.show', ['userIdOrNickname' => $article->user_nickname ?? $article->user_id, 'articleSlug' => $article->slug]),
        'title' => $article->title,
    ])
</p>
