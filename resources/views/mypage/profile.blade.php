@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">プロフィールの編集</h2>
    </div>
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <div class="v2-page-content-area">
        <div id="app-profile-edit">読み込み中...</div>
    </div>
    @endsection
