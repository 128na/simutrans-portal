@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('content')
<script id="data-article" type="application/json">
    @json($article)

</script>
<div class="v2-page v2-page-lg mb-32">
    <h2 class="v2-text-h2 mb-8 wrap-break-word">{{$article->title}}</h2>
    <div id="app-article-show">読み込み中...</div>
</div>

@endsection
