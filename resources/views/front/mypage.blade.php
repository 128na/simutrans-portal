@extends('layouts.mypage')

@section('title', __('Mypage'))

@section('content')
    <script src="{{ asset(mix('js/mypage.js')) }}" defer></script>
    <div id="app"></div>
@endsection
