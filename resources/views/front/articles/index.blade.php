@extends('layouts.front')

@section('title', $title ?? __('Articles'))
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

{{-- プロフィールカード表示 --}}
@if (isset($user))
    @section('before_title')
        @include('parts.profile-card', ['in_mypage' => false])
    @endsection
@endif
@section('content')
    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}

    <div class="list">
        @forelse ($articles as $article)
            @include('front.articles.parts.list-item', ['article' => $article])
        @empty
            <p>@lang('No article exists.')</p>
        @endforelse
    </div>

    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}
@endsection
