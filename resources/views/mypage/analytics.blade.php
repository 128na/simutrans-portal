@extends('layouts.mypage')

@section('title', __('Access Analytics'))

@section('content')
    <script>
        window.articles = @json($articles);
        console.log(window.articles);
    </script>
    <div class="mypage">
        <div id="app-analytics"></div>
    </div>
@endsection
