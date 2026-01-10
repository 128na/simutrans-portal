@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
    <script id="data-mylist" type="application/json">
        @json($mylist)
    </script>
    <div id="app-mylist-detail">読み込み中...</div>
@endsection
