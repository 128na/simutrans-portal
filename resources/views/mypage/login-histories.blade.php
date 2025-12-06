@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">ログイン履歴</h2>
        <p class="mt-2 text-secondary">
            最新10件のログイン履歴を確認できます。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-muted pt-6 lg:mx-0">
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">日時</th>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">IP</th>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">ユーザーエージェント</th>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">ログイン元</th>
                    </tr>
                <tbody>
                    @foreach($loginHistories as $loginHistory)
                    <tr>
                        <td class="border border-tertiary px-4 py-2">{{$loginHistory->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-tertiary px-4 py-2">{{$loginHistory->ip}}</td>
                        <td class="border border-tertiary px-4 py-2">{{$loginHistory->ua}}</td>
                        <td class="border border-tertiary px-4 py-2">{{$loginHistory->referer}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
