<p class="truncate">
  <span class="text-sm text-c-sub/70 mr-1">
    {{ $article['date'] }}
  </span>
  <x-ui.link :url="$article['url']" :title="$article['title']" />
</p>
