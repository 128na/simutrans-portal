@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">Pak別アドオン一覧</h2>
        <p class="mt-2 text-lg/8 text-gray-600">
            {{$meta['description']}}<br>
            記載以外のPaksetやカテゴリは @include('components.ui.link', ['url' => route('search'), 'title' => '検索']) から探せます。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        <ul class="list-none
               flex flex-col gap-y-2
               lg:grid lg:grid-cols-3 lg:gap-8">
            @foreach($pakAddonCategories as $pakSlug => $addonCategories)
            <li>
                <div class="mb-1 break-all font-bold">
                    @lang("category.pak.{$pakSlug}")
                </div>
                <ul class="list-disc text-gray-400 ml-8 break-all">
                    @foreach($addonCategories as $addonCategorie)
                    <li class="mb-1 text-black break-all">
                        @include('components.ui.link', [
                        'url' => route('categories.pakAddon', [
                        'pak' => $pakSlug,
                        'addon' => $addonCategorie->addon_slug
                        ]),
                        'title' => __("category.addon.{$addonCategorie->addon_slug}")
                        ." ({$addonCategorie->article_count})"
                        ])
                    </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
        </ul>
    </div>

</div>

@endsection
