@php
  // 公開日より後の日に更新されたものを「更新」、それ以外を「新規」とする（色だけでなく文字で示す）。
  // 公開と同じ日の更新と、公開処理で数秒ずれただけのもの（日付をまたぐ場合を含む）は「新規」に含める
  $publishedAt = $article['published_at'] ?? '';
  $isUpdated = $article['modified_at']
      && substr($article['modified_at'], 0, 10) > substr($publishedAt, 0, 10)
      && ($publishedAt === '' || strtotime($article['modified_at']) - strtotime($publishedAt) > 60);
  // pak のタグ（一覧のカードと同じ色分け。Categories.tsx の pakVariants と対応させる）。カード全体がリンクなので、タグはリンクにしない
  $pakTags = collect($article['paks'] ?? [])
      ->filter(fn (string $slug): bool => Lang::has('category.pak.'.$slug))
      ->map(fn (string $slug): array => [
          'label' => __('category.pak.'.$slug),
          'class' => match ($slug) {
              '128-japan' => 'v2-tag-pak-128-japan',
              '128' => 'v2-tag-pak-128',
              '64' => 'v2-tag-pak-64',
              'any' => 'v2-tag-pak-any',
              default => 'v2-tag-pak-other',
          },
      ]);
@endphp
<a
  href="{{ $article['url'] }}"
  class="group rounded-lg border border-c-border bg-white shadow-sm transition hover:shadow-md"
>
  <div class="aspect-video w-full overflow-hidden rounded-t-md bg-gray-100">
    <img
      src="{{ $article['thumbnail_url'] }}"
      alt=""
      class="h-full w-full object-cover"
    />
  </div>
  <div class="space-y-2 p-2">
    {{-- 「新規／更新」と主な日付は見出しの行に出す（サムネイル内の文字と重ならないよう画像には重ねない）。更新は公開日も見分けられるよう下に残す --}}
    <div class="space-y-1">
      @if ($isUpdated || $article['published_at'])
        <div class="flex flex-wrap items-center gap-x-1.5 text-meta text-c-sub">
          <span
            @class([
              'rounded border px-1.5 font-bold leading-4',
              'border-c-main bg-white text-c-main' => $isUpdated,
              'border-c-main bg-c-main text-white' => ! $isUpdated,
            ])
          >{{ $isUpdated ? '更新' : '新規' }}</span>
          @if ($isUpdated)
            <time datetime="{{ $article['modified_at'] }}">{{ substr($article['modified_at'], 0, 10) }}</time>
          @else
            <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time>
          @endif
        </div>
      @endif
      <div class="text-note font-semibold text-c-main line-clamp-2 group-hover:text-c-link">{{ $article['title'] }}</div>
    </div>
    @if ($pakTags->isNotEmpty())
      <div class="flex flex-wrap gap-1">
        @foreach ($pakTags as $tag)
          <span class="v2-tag-pak {{ $tag['class'] }} px-1.5 text-meta hover:no-underline">{{ $tag['label'] }}</span>
        @endforeach
      </div>
    @endif
    @if ($isUpdated && $article['published_at'])
      <div class="text-meta text-c-sub">公開 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time></div>
    @endif
  </div>
</a>
