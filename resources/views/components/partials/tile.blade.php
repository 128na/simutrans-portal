@php
  // 公開日より後の日に更新されたものを「更新」、それ以外を「新規」とする（色だけでなく文字で示す）。
  // 公開と同じ日の更新と、公開処理で数秒ずれただけのもの（日付をまたぐ場合を含む）は「新規」に含める
  $publishedAt = $article['published_at'] ?? '';
  $isUpdated = $article['modified_at']
      && substr($article['modified_at'], 0, 10) > substr($publishedAt, 0, 10)
      && ($publishedAt === '' || strtotime($article['modified_at']) - strtotime($publishedAt) > 60);
  // pak のタグ（一覧のカードと同じ色分け）。カード全体がリンクなので、タグはリンクにしない
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
  <div class="relative aspect-video w-full overflow-hidden rounded-t-md bg-gray-100">
    <img
      src="{{ $article['thumbnail_url'] }}"
      alt=""
      class="h-full w-full object-cover"
    />
    @if ($isUpdated || $article['published_at'])
      {{-- 下の日付の行で「新規／更新」を読み上げるので、バッジは読み上げから外す --}}
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
    @if ($pakTags->isNotEmpty())
      <div class="flex flex-wrap gap-1">
        @foreach ($pakTags as $tag)
          <span class="v2-tag-pak {{ $tag['class'] }} px-1.5 text-meta hover:no-underline">{{ $tag['label'] }}</span>
        @endforeach
      </div>
    @endif
    {{-- 「新規／更新」は画像上のバッジで見せ、日付の行では読み上げ用にだけ残す --}}
    @if ($isUpdated)
      <div class="text-meta text-c-sub">
        <span class="sr-only">更新</span><time datetime="{{ $article['modified_at'] }}">{{ substr($article['modified_at'], 0, 10) }}</time>
        @if ($article['published_at'])
          <div>公開 <time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time></div>
        @endif
      </div>
    @elseif ($article['published_at'])
      <div class="text-meta text-c-sub">
        <span class="sr-only">新規</span><time datetime="{{ $article['published_at'] }}">{{ substr($article['published_at'], 0, 10) }}</time>
      </div>
    @endif
  </div>
</a>
