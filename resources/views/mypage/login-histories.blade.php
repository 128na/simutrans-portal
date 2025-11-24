@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">ログイン履歴</h2>
        <p class="mt-2 text-gray-600">
            最新10件のログイン履歴を確認できます。
        </p>
    </div>
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">日時</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">IP</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">ユーザーエージェント</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">ログイン元</th>
                    </tr>
                <tbody>
                    @foreach($loginHistories as $loginHistory)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{$loginHistory->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{$loginHistory->ip}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{$loginHistory->ua}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{$loginHistory->referer}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
