@extends('layouts.app')

@section('title', __('message.edit-article', ['title' => $article->title]))

@section('content')
    <form method="POST" action="{{ route('mypage.articles.update.'.$article->category_post->slug, $article) }}" enctype="multipart/form-data">
        @csrf
        @include('mypage.articles._form-common')
        @include('mypage.articles._form-'.$article->category_post->slug)

        <div class="form-group">
            <button class="btn btn-lg btn-primary">{{ __('message.save') }}</button>
        </div>
    </form>
@endsection
