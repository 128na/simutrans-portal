@extends('layouts.base')

@push('styles')
  @vite('resources/js/front.ts')
  @vite('resources/css/front.css')
@endpush

@section('header')
  @include('components.layout.header-auth')
@endsection

@section('content')
  <div class="mx-auto v2-page-lg">
    <main class="min-w-0 flex-1">
      @yield('page-content')
    </main>
  </div>
@endsection
