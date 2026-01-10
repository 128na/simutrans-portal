@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('content')
<script id="data-public-mylist" type="application/json">
    @json($mylist)
</script>
<div class="v2-page v2-page-lg">
    <div id="app-public-mylist">読み込み中...</div>
</div>
@endsection
