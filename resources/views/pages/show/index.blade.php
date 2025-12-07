@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script id="data-article" type="application/json">
    @json($article)

</script>
<div class="mx-auto max-w-7xl p-6 lg:px-8 mb-32">
    <h2 class="title-xl mb-8 break-words">{{$article->title}}</h2>
    <div id="app-article-show">読み込み中...</div>
</div>

@endsection
