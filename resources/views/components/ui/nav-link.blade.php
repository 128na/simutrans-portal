@props(['url', 'title'])

@php
  // サイドメニューの項目。現在地はURLの完全一致で判定し、背景色（aria-current）で示す
  $external = ! (str_starts_with($url, config('app.url')) || str_starts_with($url, '/') || str_starts_with($url, '#'));
  $current = ! $external && rtrim($url, '/') === rtrim(url()->current(), '/');
@endphp

<a
  href="{{ $url }}"
  @if ($external) target="_blank" rel="noopener noreferrer" @endif
  @if ($current) aria-current="page" @endif
  {{ $attributes->class(['v2-nav-item', 'v2-nav-item-external' => $external]) }}
>
  {{ $title }}
</a>
