@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl">ユーザー一覧</h2>
        <p class="mt-2 text-lg/8 text-gray-600">
            {{$meta['description']}}
        </p>
    </div>
    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        @foreach($users as $user)
        <div>
            @include('components.ui.link', [
            'url' => route('users.show', ['userIdOrNickname' => $user->nickname ?? $user->id]),
            'title' => "{$user->name} ({$user->articles_count})"
            ])
        </div>
        @endforeach
    </div>
</div>

@endsection
