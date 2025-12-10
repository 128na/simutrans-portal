@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2 mb-2">ログイン履歴</h2>
        <p class="text-c-sub">
            最新10件のログイン履歴を確認できます。
        </p>
    </div>
    <div class="v2-page-content-area-lg">
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">日時</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">IP</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ユーザーエージェント</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ログイン元</th>
                    </tr>
                <tbody>
                    @foreach($loginHistories as $loginHistory)
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2">{{$loginHistory->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$loginHistory->ip}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$loginHistory->ua}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$loginHistory->referer}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
