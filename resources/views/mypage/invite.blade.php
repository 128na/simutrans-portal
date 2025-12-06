@extends('layouts.mypage')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl">招待</h2>
        <p class="mt-2 text-secondary">
            招待リンクの発行と招待したユーザーを確認できます。
        </p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-muted pt-6 lg:mx-0">
        <h4 class="title-md">招待リンク</h4>
        <div>
            <form id="revoke" action="{{route('mypage.invite')}}" method="POST" class="js-confirm" data-text="招待リンクを削除しますか？">
                @csrf
                @method('DELETE')
            </form>
            <form id="generate" action="{{route('mypage.invite')}}" method="POST">
                @csrf
                @if($user->invitation_code)
                <p class="text-lg font-semibold p-4 mb-4 text-sm text-primary rounded-lg bg-gray-50 border border-tertiary ">
                    {{route('user.invite',$user->invitation_code)}}
                </p>
                <div class="gap-x-2 flex">
                    <button type="submit" class="rounded-md bg-secondary px-4 sm:py-2 py-4 text-white cursor-pointer js-clipboard" data-text="{{route('user.invite',$user->invitation_code)}}">
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

        <h4 class="title-md">招待履歴</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">日時</th>
                        <th class="border border-tertiary px-4 py-2 bg-secondary text-white">ユーザー名</th>
                    </tr>
                <tbody>
                    @forelse($user->invites as $invite)
                    <tr>
                        <td class="border border-tertiary px-4 py-2">{{$invite->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-tertiary px-4 py-2">{{$invite->name}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="border border-tertiary px-4 py-2" colspan="2">履歴はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
