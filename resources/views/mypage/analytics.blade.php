@extends('layouts.mypage')

@section('title', __('message.analytics'))

@section('content')
    <script>
        window.articles = @json($articles);
        console.log(window.articles);
    </script>
    <div class="mypage">
        <div id="app"></div>
    </div>
@endsection
