@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">コンポーネント確認用</h2>
    </div>
    <div class="flex flex-col gap-y-12 lg:mx-0">
        <div id="app-playground"></div>
    </div>
</div>

@endsection
