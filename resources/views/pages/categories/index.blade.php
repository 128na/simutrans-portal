@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h1 mb-4">Pak別アドオン一覧</h2>
      <p class="v2-page-text-sub">
        {{ $meta['description'] }}
        <br />
        記載以外のPaksetやカテゴリは
        <x-ui.link :url="route('search')" :title="'検索'" />
        から探せます。
      </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      @foreach ($pakSections as $section)
        <div class="mb-1 break-all">
          <div class="font-semibold">
            {{ $section['label'] }}
          </div>
          <div class="ml-2 space-y-1">
            @foreach ($section['addons'] as $addon)
              <div>
                <x-ui.link :url="$addon['url']" :title="$addon['title']" />
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endsection
