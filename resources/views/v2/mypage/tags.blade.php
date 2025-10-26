@extends('v2.mypage.layout')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">タグの編集</h2>
        <p class="mt-2 text-gray-600">
            作成済みタグの説明文を編集できます。<br />
            タグの削除はできません。紐づく記事が１つもなくなると数日後に自動削除されます。
        </p>
    </div>
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">

    </div>
</div>
@endsection
