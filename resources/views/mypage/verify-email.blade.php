@extends('layouts.mypage')
@section('max-w', 'v2-page-sm')
@section('page-content')
<div class="v2-page v2-page-sm">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">メールアドレスの検証</h2>
        <p class="text-c-sub">
            登録に使用したメールアドレスが有効なものか検証します。
        </p>
    </div>
    <div class="v2-page-content-area-lg">
        <form action="{{route('verification.send')}}" method="POST">
            @csrf
            <button type="submit" class="v2-button v2-button-lg v2-button-primary">
                メールを再送信する
            </button>
        </form>
    </div>
</div>

@endsection
