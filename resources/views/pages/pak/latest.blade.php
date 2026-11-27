@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="space-y-8">
      <h2 class="v2-text-h3">新着アドオン</h2>
      <nav aria-label="pak を選ぶ" class="flex flex-wrap gap-x-5 gap-y-3">
        <x-ui.link url="#pak128-japan" title="Pak128Japan" />
        <x-ui.link url="#pak128" title="Pak128" />
        <x-ui.link url="#pak64" title="Pak64" />
        <x-ui.link url="#pak256-longscale" title="Pak256.LongScale" />
        <x-ui.link :url="route('pak.others')" title="その他の pak 一覧" />
      </nav>
      <p class="text-sm text-c-sub">このページには上の4種類の pak の新着を表示しています。</p>
      <div id="pak128-japan" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.128japan')" :title="'Pak128Japan'" />
        </h3>
        @if ($pak128Japan !== [])
          <div class="mb-4">
            @include('components.partials.latest-cards', ['key' => 'pak128-japan', 'cards' => $pak128Japan])
          </div>
        @endif
      </div>
      <div id="pak128" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.128')" :title="'Pak128'" />
        </h3>
        @if ($pak128 !== [])
          <div class="mb-4">
            @include('components.partials.latest-cards', ['key' => 'pak128', 'cards' => $pak128])
          </div>
        @endif
      </div>
      <div id="pak64" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.64')" :title="'Pak64'" />
        </h3>
        @if ($pak64 !== [])
          <div class="mb-4">
            @include('components.partials.latest-cards', ['key' => 'pak64', 'cards' => $pak64])
          </div>
        @endif
      </div>
      <div id="pak256-longscale" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.256longscale')" :title="'Pak256.LongScale'" />
        </h3>
        @if ($pak256LongScale !== [])
          <div class="mb-4">
            @include('components.partials.latest-cards', ['key' => 'pak256-longscale', 'cards' => $pak256LongScale])
          </div>
        @else
          <p class="text-note text-c-sub">まだアドオンが登録されていません。</p>
        @endif
      </div>
    </div>
  </div>
@endsection
