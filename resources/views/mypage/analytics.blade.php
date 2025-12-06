@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">アナリティクス</h2>
    </div>
    <script id="data-articles" type="application/json">
        @json($articles)

    </script>
    <div class="flex flex-col gap-y-12 border-t border-muted pt-6 lg:mx-0">
        <div id="app-analytics"></div>
    </div>
    @endsection
