@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">リダイレクトの設定</h2>
      <p class="text-c-sub">
        記事のURLを変更したときに、自動で作成されるリダイレクト設定です。旧URLから新URLへ転送されます。
      </p>
    </div>
    <div class="v2-page-content-area-lg">
      <div class="v2-table-wrapper">
        <table class="v2-table v2-table-fixed">
          <thead>
            <tr>
              <th class="w-3/12">作成日時</th>
              <th class="w-4/12">転送元</th>
              <th class="w-4/12">転送先</th>
              <th class="w-1/12">操作</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($redirects as $redirect)
              <tr>
                <td>{{ $redirect->created_at->format('Y/m/d H:i:s') }}</td>
                <td>{{ urldecode($redirect->from) }}</td>
                <td>{{ urldecode($redirect->to) }}</td>
                <td>
                  <form
                    method="POST"
                    action="{{ route('mypage.redirects.destroy', [$redirect->id]) }}"
                    class="js-confirm"
                    data-text="リダイレクト設定を削除しますか？"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="v2-button v2-button-danger v2-button-md"
                    >
                      削除
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">設定はありません</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
