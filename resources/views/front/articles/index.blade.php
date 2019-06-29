@extends('layouts.app')

@section('title', $title ?? 'Articles')
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

{{-- プロフィールカード表示 --}}
@if (isset($user))
    @section('before_title')
        @include('parts.profile-card', ['in_mypage' => false])
    @endsection
@endif
@section('content')
    {!! e($articles->links()) !!}

    <div class="list">
        @forelse ($articles as $article)
            @include('front.articles.parts.list-item', ['article' => $article])
        @empty
            <p>{{ __('message.no-article')}}</p>
        @endforelse
    </div>

    {!! e($articles->links()) !!}
@endsection
