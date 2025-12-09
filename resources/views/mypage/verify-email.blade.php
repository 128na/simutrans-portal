@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">メールアドレスの検証</h2>
        <p class="mt-2 text-c-sub">
            登録に使用したメールアドレスが有効なものか検証します。
        </p>
    </div>
    <div class="v2-page-content-area">
        <form action="{{route('verification.send')}}" method="POST">
            @csrf
            <button type="submit" class="button-primary">
                送信する
            </button>
        </form>
    </div>
</div>

@endsection
