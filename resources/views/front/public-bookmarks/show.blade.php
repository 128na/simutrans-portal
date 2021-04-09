@extends('layouts.front')

@section('id', 'article-show')
@section('title', $title)

@section('content')
    <article>
        <h2>{{ $item->title }}</h2>
    </article>
@endsection
