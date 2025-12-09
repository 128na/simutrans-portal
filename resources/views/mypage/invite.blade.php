@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">招待</h2>
        <p class="mt-2 text-c-sub">
            招待リンクの発行と招待したユーザーを確認できます。
        </p>
    </div>
    <div class="pt-6 v2-page-content-area">
        <h4 class="v2-text-h3">招待リンク</h4>
        <div>
            <form id="revoke" action="{{route('mypage.invite')}}" method="POST" class="js-confirm" data-text="招待リンクを削除しますか？">
                @csrf
                @method('DELETE')
            </form>
            <form id="generate" action="{{route('mypage.invite')}}" method="POST">
                @csrf
                @if($user->invitation_code)
                <p class="text-lg font-semibold p-4 mb-4 text-sm rounded-lg bg-gray-50 border border-c-sub/10 ">
                    {{route('user.invite',$user->invitation_code)}}
                </p>
                <div class="gap-x-2 flex">
                    <button type="submit" class="button-sub js-clipboard" data-text="{{route('user.invite',$user->invitation_code)}}">
                        リンクをコピー
                    </button>
                    <button type="submit" class="button-primary">
                        再発行
                    </button>
                    <button type="submit" class="button-danger" form="revoke">
                        削除
                    </button>
                </div>
                @else
                <button type="submit" class="button-primary">
                    発行
                </button>
                @endif
            </form>
        </div>

        <h4 class="v2-text-h3">招待履歴</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">日時</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ユーザー名</th>
                    </tr>
                <tbody>
                    @forelse($user->invites as $invite)
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2">{{$invite->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$invite->name}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2" colspan="2">履歴はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
