@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">招待</h2>
        <p class="mt-2 text-gray-600">
            招待リンクの発行と招待したユーザーを確認できます。
        </p>
    </div>
    <div class="mt-10 flex flex-col gap-y-4 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <h3 class="text-2xl font-semibold text-brand sm:text-2xl my-4">招待リンク</h3>
        <div>
            <form id="revoke" action="{{route('mypage.invite')}}" method="POST" class="js-confirm" data-text="招待リンクを削除しますか？">
                @csrf
                @method('DELETE')
            </form>
            <form id="generate" action="{{route('mypage.invite')}}" method="POST">
                @csrf
                @if($user->invitation_code)
                <p class="text-lg font-bold p-4 mb-4 text-sm text-gray-900 rounded-lg bg-gray-50 border border-gray-300 ">
                    {{route('user.invite',$user->invitation_code)}}
                </p>
                <div class="gap-x-2 flex">
                    <button type="submit" class="rounded-md bg-gray-500 px-4 sm:py-2 py-4 text-white cursor-pointer js-clipboard" data-text="{{route('user.invite',$user->invitation_code)}}">
                        リンクをコピー
                    </button>
                    <button type="submit" class="rounded-md bg-brand px-4 sm:py-2 py-4 text-white cursor-pointer">
                        再発行
                    </button>
                    <button type="submit" class="rounded-md bg-red-500 px-4 sm:py-2 py-4 text-white cursor-pointer" form="revoke">
                        削除
                    </button>
                </div>
                @else
                <button type="submit" class="rounded-md bg-brand px-4 sm:py-2 py-4 text-white cursor-pointer">
                    発行
                </button>
                @endif
            </form>
        </div>

        <h3 class="text-2xl font-semibold text-brand sm:text-2xl my-4">招待履歴</h3>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">日時</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">ユーザー名</th>
                    </tr>
                <tbody>
                    @forelse($user->invites as $invite)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{$invite->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{$invite->name}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="border border-gray-300 px-4 py-2" colspan="2">履歴はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
