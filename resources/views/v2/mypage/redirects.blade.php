@extends('v2.mypage.layout')
@section('max-w', 'max-w-7xl')
@section('content')
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-3xl font-semibold text-pretty text-gray-900 sm:text-3xl">リダイレクトの設定</h2>
        <p class="mt-2 text-gray-600">
            記事のURLを変更したときに、自動で作成されるリダイレクト設定です。旧URLから新URLへ転送されます。
        </p>
    </div>
    <div class="mt-10 flex flex-col gap-y-12 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">作成日時</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">転送元</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">転送先</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">操作</th>
                    </tr>
                <tbody>
                    @forelse($redirects as $redirect)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{$redirect->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{urldecode($redirect->from)}}</td>
                        <td class="border border-gray-300 px-4 py-2">{{urldecode($redirect->to)}}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form method="POST" action="{{route('mypage.redirects.destroy', [$redirect->id])}}" class="js-confirm" data-text="リダイレクト設定を削除しますか？">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-500 px-4 sm:py-2 py-4 text-white cursor-pointer">
                                    削除
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="border border-gray-300 px-4 py-2" colspan="4">設定はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
