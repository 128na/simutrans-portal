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

    <section class="mb-4 list">
        @component('components.addon-list', ['articles' => $articles])
            @slot('no_item')
                @lang('No article exists.')
            @endslot
        @endcomponent
    </section>

    {!! e($articles->onEachSide(1)->links('vendor.pagination.default')) !!}
@endsection
