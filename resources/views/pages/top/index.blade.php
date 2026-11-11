@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="absolute inset-0 -z-10 overflow-hidden">
      <svg
        aria-hidden="true"
        class="absolute top-0 left-[max(50%,25rem)] h-256 w-512 -translate-x-1/2 mask-[radial-gradient(64rem_64rem_at_top,white,transparent)] stroke-c-sub/10"
      >
        <defs>
          <pattern
            id="e813992c-7d03-4cc4-a2bd-151760b470a0"
            width="200"
            height="200"
            x="50%"
            y="-1"
            patternUnits="userSpaceOnUse"
          >
            <path d="M100 200V.5M.5 .5H200" fill="none" />
          </pattern>
        </defs>
        <svg x="50%" y="-1" class="overflow-visible fill-gray-50">
          <path
            d="M-100.5 0h201v201h-201Z M699.5 0h201v201h-201Z M499.5 400h201v201h-201Z M-300.5 600h201v201h-201Z"
            stroke-width="0"
          />
        </svg>
        <rect
          width="100%"
          height="100%"
          fill="url(#e813992c-7d03-4cc4-a2bd-151760b470a0)"
          stroke-width="0"
        />
      </svg>
    </div>
    <div class="v2-page-content-area-lg">
      <div class="space-y-10">
        <section class="space-y-4" aria-labelledby="top-latest-heading">
          <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
            <h1 id="top-latest-heading" class="v2-text-h3">新着アドオン</h1>
            <p class="text-note text-c-sub">{{ config('app.name') }} は Simutrans のアドオンを投稿・紹介できるポータルサイトです。</p>
          </div>
          <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <nav aria-label="新着の pak を切り替える" class="flex flex-wrap items-center gap-2">
              <a
                href="{{ route('index') }}"
                class="v2-chip"
                @if ($selectedPak === null) aria-current="true" @endif
              >すべて</a>
              @foreach ($pakTabs as $tab)
                <a
                  href="{{ $tab['url'] }}"
                  class="v2-chip"
                  @if ($selectedPak === $tab['slug']) aria-current="true" @endif
                >{{ $tab['label'] }}</a>
              @endforeach
            </nav>
            <div class="sm:ml-auto">
              <x-ui.link :url="$latestMoreUrl" :title="'新着アドオンをもっと見る'" />
            </div>
          </div>
          @if ($latestCards !== [])
            @include('components.partials.latest-cards', ['key' => 'top', 'cards' => $latestCards])
          @else
            <p class="text-note text-c-sub">新着を表示できませんでした。上のリンクから一覧をご覧ください。</p>
          @endif
        </section>

        <section class="space-y-4" aria-labelledby="top-addon-heading">
          <h2 id="top-addon-heading" class="v2-text-h4">種類から探す</h2>
          <div class="grid gap-4 md:grid-cols-3">
            @foreach ($addonSections as $section)
              <div class="flex flex-col gap-3 rounded-lg border border-c-border p-4">
                <div><x-ui.link :url="$section['url']" :title="$section['label']" /></div>
                <ul class="flex flex-wrap gap-2">
                  @foreach ($section['addons'] as $addon)
                    <li>
                      <a href="{{ $addon['url'] }}" class="v2-chip">
                        {{ $addon['label'] }}
                        @if ($addon['count'] !== null)
                          <span class="ml-1.5 text-c-sub">{{ number_format($addon['count']) }}<span class="sr-only">件</span></span>
                        @endif
                      </a>
                    </li>
                  @endforeach
                  <li><a href="{{ $section['url'] }}" class="v2-chip">すべての種類</a></li>
                </ul>
              </div>
            @endforeach
          </div>
        </section>

        <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
          <section>
            <h2 class="v2-text-h4 mb-4">
              <x-ui.link :url="route('announces')" :title="'お知らせ'" />
            </h2>
            <div class="gap-2 flex flex-col">
              @foreach ($announces as $article)
                @include('components.partials.one-liner', ['article' => $article])
              @endforeach
            </div>
          </section>
          <section class="space-y-4">
            <h2 class="v2-text-h4">関連サイトのリンク</h2>
            <div class="gap-2 grid">
              <x-ui.link :url="'https://forum.simutrans.com/'" :title="'International Simutrans Forum'" />
              <x-ui.link :url="'https://japanese.simutrans.com/index.php'" :title="'Simutrans 日本語化･解説'" />
              <x-ui.link :url="'https://wikiwiki.jp/twitrans/'" :title="'Simutrans 的な実験室'" />
              <x-ui.link :url="'https://cross-search.128-bit.net/'" :title="'Simutrans 横断検索'" />
              <x-ui.link :url="route('redirect', ['name' => 'simutrans-interact-meeting'])" :title="'シムトランス交流会議'" />
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
@endsection
