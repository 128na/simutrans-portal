@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="space-y-8">
        <h2 class="v2-text-h3">新着アドオン</h2>
        <div>
            <h3 class="v2-text-h4 mb-4">@include('components.ui.link', ['url' => route('pak.128japan'), 'title' => 'Pak128Japan'])</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach($pak128Japan as $article)
                @include('components.partials.tile', ['article' => $article])
                @endforeach
            </div>
        </div>
        <div>
            <h3 class="v2-text-h4 mb-4">@include('components.ui.link', ['url' => route('pak.128'), 'title' => 'Pak128'])</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach($pak128 as $article)
                @include('components.partials.tile', ['article' => $article])
                @endforeach
            </div>
        </div>
        <div>
            <h3 class="v2-text-h4 mb-4">@include('components.ui.link', ['url' => route('pak.64'), 'title' => 'Pak64'])</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach($pak64 as $article)
                @include('components.partials.tile', ['article' => $article])
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
