@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">ユーザー一覧</h2>
        <p class="v2-page-text-sub">
            {{$meta['description']}}
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-1">
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
