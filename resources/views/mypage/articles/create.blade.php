@extends('layouts.mypage')

@section('title', __('Create Article'))

@section('content')
    <script>
        const appdata_article = null;
    </script>
    <script src="{{ asset(mix('js/editor.js')) }}" defer></script>
    <div id="app"></div>
@endsection
