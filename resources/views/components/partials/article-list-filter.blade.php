@php
  /** @var \App\Services\Front\ArticleListFilter $filter */
  $showAddon ??= true;
  // 入手方法のどちらを選択中にするか。検索画面は詳細条件の「投稿形式」と同じ postTypes を使うので、
  // 検索の条件（正規化後）から渡し、投稿形式がほかの組み合わせのときはどちらも選択中にしない
  $acquisitionDownloadOnly ??= $filter->downloadOnly;
  $acquisitionAll ??= ! $acquisitionDownloadOnly;
  $total ??= null;
  // 種類ごとの件数（slug => 件数）。保存済みの表示データがある pak 別一覧だけで渡し、それ以外は件数を出さない
  /** @var array<string, int>|null $addonCounts */
  $addonCounts ??= null;
  // 件数が無い一覧で常に出す主要な種類（検索画面だけで渡す）。無ければ件数が無い一覧は畳まない
  /** @var list<string>|null $addonPrimary */
  $addonPrimary ??= null;
  // 0 件の種類は出さず、件数の少ない種類は「ほか n 種類」に畳む
  $addonChips = match (true) {
    ! $showAddon => null,
    $addonCounts === null && $addonPrimary !== null => $filter->addonChipGroupsByPrimary($addonPrimary),
    default => $filter->addonChipGroups($addonCounts),
  };
@endphp
<div class="flex flex-col gap-2 rounded-lg bg-c-surface p-3">
  @if ($showAddon)
    <nav aria-label="種類で絞り込む" class="relative flex items-center gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:pb-0">
      <span class="shrink-0 px-1 text-meta font-bold text-c-sub">種類</span>
      <a
        href="{{ request()->fullUrlWithQuery(['addon' => null, 'page' => null]) }}"
        class="v2-chip"
        @if ($filter->addonSlug === null) aria-current="true" @endif
      >すべて</a>
      @foreach ($addonChips['visible'] as $slug => $label)
        @include('components.partials.article-list-addon-chip')
      @endforeach
      @if ($addonChips['folded'] !== [])
        {{-- 開くと PC 幅では次の行に折り返して並べる。スマホ幅は「閉じる」の下に横 1 列で並べ、行ごと横にスクロールする --}}
        <details class="group shrink-0 sm:open:basis-full">
          <summary class="v2-chip cursor-pointer list-none [&::-webkit-details-marker]:hidden">
            <span class="group-open:hidden">ほか {{ count($addonChips['folded']) }} 種類</span>
            <span class="hidden group-open:inline">閉じる</span>
          </summary>
          <div class="mt-2 flex gap-2 sm:flex-wrap">
            @foreach ($addonChips['folded'] as $slug => $label)
              @include('components.partials.article-list-addon-chip')
            @endforeach
          </div>
        </details>
      @endif
    </nav>
  @endif
  <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
    <nav aria-label="入手方法で絞り込む" class="flex flex-wrap items-center gap-2">
      <span class="shrink-0 px-1 text-meta font-bold text-c-sub">入手方法</span>
      <a
        href="{{ request()->fullUrlWithQuery(['postTypes' => null, 'page' => null]) }}"
        class="v2-chip"
        @if ($acquisitionAll) aria-current="true" @endif
      >すべて</a>
      <a
        href="{{ request()->fullUrlWithQuery(['postTypes' => [\App\Enums\ArticlePostType::AddonPost->value], 'page' => null]) }}"
        class="v2-chip"
        @if ($acquisitionDownloadOnly) aria-current="true" @endif
      >ダウンロードできるものだけ</a>
    </nav>
    @if ($total !== null)
      {{-- 件数は並び順と同じ行に置き、一覧の開始位置を上げる --}}
      <p class="px-1 text-note text-c-sub sm:ml-auto">{{ number_format($total) }} 件</p>
    @endif
    <nav aria-label="並び順" class="flex flex-wrap items-center gap-2 @if ($total === null) sm:ml-auto @endif">
      <span class="shrink-0 px-1 text-meta font-bold text-c-sub">並び順</span>
      @foreach (\App\Enums\ArticleListSort::cases() as $sort)
        <a
          href="{{ request()->fullUrlWithQuery(['sort' => $sort === \App\Enums\ArticleListSort::Modified ? null : $sort->value, 'page' => null]) }}"
          class="v2-chip"
          @if ($filter->sort === $sort) aria-current="true" @endif
          @if ($sort->note() !== null) title="{{ $sort->note() }}" @endif
        >{{ $sort->label() }}</a>
      @endforeach
    </nav>
  </div>
  @if ($filter->sort->note() !== null)
    {{-- 選んだ並び順に補足があるときだけ出す。スマホ幅でも 1〜2 行に収まる短さにする --}}
    <p class="px-1 text-note text-c-sub">{{ $filter->sort->note() }}</p>
  @endif
</div>
