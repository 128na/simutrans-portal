<a
  href="{{
    route('articles.show', [
        'userIdOrNickname' => $article->user->nickname ?? $article->user->id,
        'articleSlug' => $article->slug,
    ])
  }}"
  class="group rounded-lg border border-c-sub/10 bg-white shadow-sm transition hover:shadow-md"
>
  <div class="aspect-video w-full overflow-hidden rounded-t-md bg-gray-100">
    <img
      src="{{ $article->thumbnail_url }}"
      alt="{{ $article->title }}"
      class="h-full w-full object-cover"
    />
  </div>
  <div class="mt-2 text-sm font-medium text-c-main line-clamp-2 px-2">
    {{ $article->title }}
  </div>
</a>
