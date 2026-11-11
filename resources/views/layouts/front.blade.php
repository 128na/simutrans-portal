@extends('layouts.base')

@push('styles')
  @vite('resources/js/front.ts')
  @vite('resources/css/front.css')
@endpush

@section('header')
  @include('components.layout.header')
@endsection

@section('content')
  <div class="mx-auto v2-page-lg">
    <div class="lg:flex lg:gap-8">
      {{-- 記事詳細など本文幅を広く取るページは hide-sidebar を指定してサイドメニューを外す（モバイルメニューは残る） --}}
      @sectionMissing('hide-sidebar')
        <aside class="hidden lg:block w-64 shrink-0 border-r border-c-sub/10">
          <div class="sticky top-6 p-3 lg:py-4 lg:pr-3">
            @include('components.layout.sidebar-front')
          </div>
        </aside>
      @endif
      <main class="min-w-0 flex-1">
        @yield('page-content')
      </main>
    </div>
  </div>
@endsection
