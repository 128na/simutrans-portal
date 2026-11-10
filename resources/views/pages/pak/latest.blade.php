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
        <x-ui.link :url="route('pak.others')" title="その他の pak 一覧" />
      </nav>
      <p class="text-sm text-c-sub">このページには上の3種類の pak の新着を表示しています。</p>
      <div id="pak128-japan" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.128japan')" :title="'Pak128Japan'" />
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
          @foreach ($pak128Japan as $article)
            @include('components.partials.tile', ['article' => $article])
          @endforeach
        </div>
      </div>
      <div id="pak128" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.128')" :title="'Pak128'" />
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
          @foreach ($pak128 as $article)
            @include('components.partials.tile', ['article' => $article])
          @endforeach
        </div>
      </div>
      <div id="pak64" class="scroll-mt-24">
        <h3 class="v2-text-h4 mb-4">
          <x-ui.link :url="route('pak.64')" :title="'Pak64'" />
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
          @foreach ($pak64 as $article)
            @include('components.partials.tile', ['article' => $article])
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection
