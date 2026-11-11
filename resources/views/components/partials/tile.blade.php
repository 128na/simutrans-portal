@php
  // 公開日より後の日に更新されたものを「更新」、それ以外を「新規」とする（色だけでなく文字で示す）。
  // 公開と同じ日の更新（公開処理で数秒ずれる場合を含む）は「新規」に含める
  $isUpdated = $article['modified_at'] && substr($article['modified_at'], 0, 10) > substr($article['published_at'] ?? '', 0, 10);
@endphp
<a
  href="{{ $article['url'] }}"
  class="group rounded-lg border border-c-border bg-white shadow-sm transition hover:shadow-md"
>
  <div class="relative aspect-video w-full overflow-hidden rounded-t-md bg-gray-100">
    <img
      src="{{ $article['thumbnail_url'] }}"
      alt=""
      class="h-full w-full object-cover"
    />
    @if ($isUpdated || $article['published_at'])
      {{-- 下の日付の行でも「新規／更新」を読み上げるので、バッジは読み上げから外す --}}
      <span
        aria-hidden="true"
        @class([
          'absolute top-1.5 left-1.5 rounded border px-1.5 text-meta font-bold leading-5',
          'border-c-main bg-white text-c-main' => $isUpdated,
          'border-c-main bg-c-main text-white' => ! $isUpdated,
        ])
      >{{ $isUpdated ? '更新' : '新規' }}</span>
    @endif
  </div>
  <div class="space-y-2 p-2">
    <div class="text-note font-semibold text-c-main line-clamp-2 group-hover:text-c-link">{{ $article['title'] }}</div>
    @if ($isUpdated)
      <div class="text-meta text-c-sub">
        更新 <time datetime="{{ $article['modified_at'] }}">{{ substr($article['modified_at'], 0, 10) }}</time>
        @if ($article['published_at'])
          <div>公開 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time></div>
        @endif
      </div>
    @elseif ($article['published_at'])
      <div class="text-meta text-c-sub">
        新規 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time>
      </div>
    @endif
  </div>
</a>
