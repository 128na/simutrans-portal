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
        <div class="v2-table-wrapper">
            <table class="v2-table v2-table-fixed">
                <thead>
                    <tr>
                        <th class="w-2/12">日時</th>
                        <th class="w-3/12">IP</th>
                        <th class="w-4/12">ユーザーエージェント</th>
                        <th class="w-3/12">ログイン元</th>
                    </tr>
                <tbody>
                    @foreach($loginHistories as $loginHistory)
                    <tr>
                        <td>{{$loginHistory->created_at->format('Y/m/d H:i:s')}}</td>
                        <td>{{$loginHistory->ip}}</td>
                        <td>{{$loginHistory->ua}}</td>
                        <td>{{$loginHistory->referer}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
