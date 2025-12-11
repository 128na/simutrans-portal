<p>
    <span class="text-sm text-c-sub/70 mr-1">
        {{$article->published_at->format('Y/m/d')}}
    </span>
    @include('components.ui.link', ['url' => route('articles.show', ['userIdOrNickname' => $article->user_nickname ?? $article->user_id, 'articleSlug' => $article->slug]), 'title' => $article->title])
</p>
