@extends('layouts.app')

@section('title', __('message.edit-article', ['title' => $article->title]))

@section('content')
    <form method="POST" action="{{ route('mypage.articles.update.'.$article->post_type, $article) }}" enctype="multipart/form-data">
        @csrf
        @include('parts._form-common')
        @include('parts._form-'.$article->post_type)
        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">{{ __('message.save') }}</button>
        </div>
    </form>
@endsection
