@extends('layouts.app')

@section('title', 'Edit '.$article->title)

@section('content')
    <form method="POST" action="{{ route('mypage.articles.update.'.$article->category_post->slug, $article) }}" enctype="multipart/form-data">
        @csrf
        @include('mypage.articles._form-common')
        @include('mypage.articles._form-'.$article->category_post->slug)

        <div class="form-group">
            <button class="btn btn-lg btn-primary">Submit</button>
        </div>
    </form>
    <script src="{{ asset('js/form.js') }}" defer></script>
@endsection
