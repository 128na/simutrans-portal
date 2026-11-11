@php
  /** @var \App\Services\Front\ArticleListFilter $filter */
  $showAddon ??= true;
  // 検索画面は詳細条件の「投稿形式」で postTypes を選ぶので、入手方法の切り替えは出さない
  $showAcquisition ??= true;
  $total ??= null;
@endphp
<div class="flex flex-col gap-3 rounded-lg bg-c-surface p-3">
  @if ($showAddon)
    <nav aria-label="種類で絞り込む" class="flex items-center gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:pb-0">
      <span class="shrink-0 px-1 text-meta font-bold text-c-sub">種類</span>
      <a
        href="{{ request()->fullUrlWithQuery(['addon' => null, 'page' => null]) }}"
        class="v2-chip"
        @if ($filter->addonSlug === null) aria-current="true" @endif
      >すべて</a>
      @foreach (\App\Services\Front\ArticleListFilter::addonOptions() as $slug => $label)
        <a
          href="{{ request()->fullUrlWithQuery(['addon' => $slug, 'page' => null]) }}"
          class="v2-chip"
          @if ($filter->addonSlug === $slug) aria-current="true" @endif
        >{{ $label }}</a>
      @endforeach
    </nav>
  @endif
  <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
    @if ($showAcquisition)
      <nav aria-label="入手方法で絞り込む" class="flex flex-wrap items-center gap-2">
        <span class="shrink-0 px-1 text-meta font-bold text-c-sub">入手方法</span>
        <a
          href="{{ request()->fullUrlWithQuery(['postTypes' => null, 'page' => null]) }}"
          class="v2-chip"
          @if (! $filter->downloadOnly) aria-current="true" @endif
        >すべて</a>
        <a
          href="{{ request()->fullUrlWithQuery(['postTypes' => [\App\Enums\ArticlePostType::AddonPost->value], 'page' => null]) }}"
          class="v2-chip"
          @if ($filter->downloadOnly) aria-current="true" @endif
        >ダウンロードできるものだけ</a>
      </nav>
    @endif
    <nav aria-label="並び順" class="flex flex-wrap items-center gap-2 sm:ml-auto">
      <span class="shrink-0 px-1 text-meta font-bold text-c-sub">並び順</span>
      @foreach (\App\Enums\ArticleListSort::cases() as $sort)
        <a
          href="{{ request()->fullUrlWithQuery(['sort' => $sort === \App\Enums\ArticleListSort::Modified ? null : $sort->value, 'page' => null]) }}"
          class="v2-chip"
          @if ($filter->sort === $sort) aria-current="true" @endif
        >{{ $sort->label() }}</a>
      @endforeach
    </nav>
  </div>
  @if ($total !== null)
    <p class="px-1 text-note text-c-sub">{{ number_format($total) }} 件</p>
  @endif
</div>
