{{-- 種類の切り替えのチップ 1 つ。article-list-filter から $slug・$label・$filter・$addonCounts を受け取る --}}
<a
  href="{{ request()->fullUrlWithQuery(['addon' => $slug, 'page' => null]) }}"
  class="v2-chip"
  @if ($filter->addonSlug === $slug) aria-current="true" @endif
>{{ $label }}@if ($addonCounts !== null)<span class="ml-1.5 opacity-75">{{ number_format($addonCounts[$slug] ?? 0) }}<span class="sr-only">件</span></span>@endif</a>
