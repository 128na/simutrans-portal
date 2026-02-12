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
        @include('components.ui.link', ['url' => route('search'), 'title' => '検索'])
        から探せます。
      </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      @foreach ($pakAddonCategories as $pakSlug => $addonCategories)
        <div class="mb-1 break-all">
          <div class="font-semibold">
            @lang("category.pak.{$pakSlug}")
          </div>
          <div class="ml-2 space-y-1">
            @foreach ($addonCategories as $addonCategorie)
              <div>
                @include('components.ui.link', [
                    'url' => route('categories.pakAddon', [
                        'pak' => $pakSlug,
                        'addon' => $addonCategorie->addon_slug,
                    ]),
                    'title' => __("category.addon.{$addonCategorie->addon_slug}")
                    ." ({$addonCategorie->article_count})",
                ])
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endsection
