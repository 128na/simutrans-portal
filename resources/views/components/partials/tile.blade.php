<a
  href="{{ $article['url'] }}"
  class="group rounded-lg border border-c-sub/10 bg-white shadow-sm transition hover:shadow-md"
>
  <div class="aspect-video w-full overflow-hidden rounded-t-md bg-gray-100">
    <img
      src="{{ $article['thumbnail_url'] }}"
      alt="{{ $article['title'] }}"
      class="h-full w-full object-cover"
    />
  </div>
  <div class="space-y-2 p-2">
    <div class="text-sm font-medium text-c-main line-clamp-2">{{ $article['title'] }}</div>
    @if ($article['modified_at'] && $article['modified_at'] > ($article['published_at'] ?? ''))
      <div class="text-xs text-c-sub">
        更新 <time datetime="{{ $article['modified_at'] }}">{{ substr($article['modified_at'], 0, 10) }}</time>
        @if ($article['published_at'])
          <div>公開 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time></div>
        @endif
      </div>
    @elseif ($article['published_at'])
      <div class="text-xs text-c-sub">
        新規 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time>
      </div>
    @endif
  </div>
</a>
