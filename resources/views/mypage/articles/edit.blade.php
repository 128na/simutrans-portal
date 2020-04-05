@extends('layouts.mypage')

@section('title', __('Edit :title', ['title' => $article->title]))

@section('content')
    <script>
        const appdata_article = @json($article);
    </script>
    <script src="{{ asset(mix('js/editor.js')) }}" defer></script>
    <div id="app"></div>
@endsection
