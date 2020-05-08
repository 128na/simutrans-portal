@extends('layouts.front')

@section('id', 'listing')
@section('title', $title)
@section('meta-description', config('app.meta-description'))
@section('meta-image', asset('storage/'.config('app.meta-image')))

@section('content')
    @foreach ($tags as $tag)
        <a class="btn btn-outline-secondary m-2" href="{{ route('tag', $tag) }}">{{$tag->name}} ({{ $tag->articles_count }})</a>
    @endforeach

    <script type="application/ld+json">
        @json($schemas)
    </script>
@endsection
