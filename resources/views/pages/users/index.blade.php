@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">ユーザー一覧</h2>
        <p class="mt-2 text-lg/8 text-secondary">
            {{$meta['description']}}
        </p>
    </div>
    <div class="mt-10 border-t border-muted pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
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
