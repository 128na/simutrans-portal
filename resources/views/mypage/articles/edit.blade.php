@extends('layouts.mypage')

@section('title', __('Edit :title', ['title' => $article->title]))

@section('content')
    <form method="POST" action="{{ route('mypage.articles.update.'.$article->post_type, $article) }}"
        class="js-previewable-form" data-preview-action="{{ route('mypage.articles.update.'.$article->post_type, [$article,'preview'], false) }}">
        @csrf
        @include('parts._form-common')
        @include('parts._form-'.$article->post_type)
        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">{{ __('Save') }}</button>
            <button class="btn btn btn-secondary js-open-preview">{{ __('Preview') }}</button>
        </div>
    </form>
@endsection
