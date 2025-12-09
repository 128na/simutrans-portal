@extends('layouts.mypage')
@section('max-w', '2-content-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">リダイレクトの設定</h2>
        <p class="mt-2 text-c-sub">
            記事のURLを変更したときに、自動で作成されるリダイレクト設定です。旧URLから新URLへ転送されます。
        </p>
    </div>
    <div class="v2-page-content-area">
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">作成日時</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">転送元</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">転送先</th>
                        <th class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">操作</th>
                    </tr>
                <tbody>
                    @forelse($redirects as $redirect)
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2">{{$redirect->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{urldecode($redirect->from)}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{urldecode($redirect->to)}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            <form method="POST" action="{{route('mypage.redirects.destroy', [$redirect->id])}}" class="js-confirm" data-text="リダイレクト設定を削除しますか？">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger">
                                    削除
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2" colspan="4">設定はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
