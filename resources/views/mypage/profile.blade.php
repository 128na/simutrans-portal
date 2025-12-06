@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">プロフィールの編集</h2>
    </div>
    <script id="data-user" type="application/json">
        @json($user)

    </script>
    <script id="data-attachments" type="application/json">
        @json($attachments)

    </script>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        <div id="app-profile-edit"></div>
    </div>
    @endsection
