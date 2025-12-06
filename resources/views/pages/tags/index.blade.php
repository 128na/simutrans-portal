@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">タグ一覧</h2>
        <p class="mt-2 text-lg/8 text-secondary">
            {{$meta['description']}}
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        @foreach($tags as $tag)
        <div>
            @include('components.ui.link', [
            'url' => route('tags.show', ['tag' => $tag]),
            'title' => "{$tag->name} ({$tag->articles_count})"
            ])
        </div>
        @endforeach
    </div>
</div>

@endsection
