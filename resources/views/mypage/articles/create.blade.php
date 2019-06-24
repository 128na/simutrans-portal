@extends('layouts.app')

@section('title', __('message.create-article-of', ['type' => __('category.post.'.$post_type)]))

@section('content')
    <form method="POST" action="{{ route('mypage.articles.store.'.$post_type) }}" enctype="multipart/form-data">
        @csrf
        @include('parts._form-common')
        @include('parts._form-'.$post_type)
        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">{{ __('message.save') }}</button>
        </div>
    </form>
@endsection
