@extends('layouts.app')

@section('title', 'New Article')

@section('content')
<h1>New Article</h1>
<form method="POST" action="{{ route('mypage.articles.store') }}" enctype="multipart/form-data">
    @include('mypage.articles._form')
</form>
@endsection
