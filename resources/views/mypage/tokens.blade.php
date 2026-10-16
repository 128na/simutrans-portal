@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">APIトークン</h2>
      <p class="text-c-sub">
        APIトークンの新規発行は終了しました。発行済みトークンの確認と削除ができます。
      </p>
    </div>

    <div class="v2-page-content-area-lg">
      <div>
        <h4 class="v2-text-h3 mb-4">発行済みトークン</h4>
        <div class="v2-table-wrapper">
          <table class="v2-table">
            <thead>
              <tr>
                <th>名前</th>
                <th>発行日時</th>
                <th>最終使用日時</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tokens as $token)
                <tr>
                  <td>{{ $token->name }}</td>
                  <td>{{ $token->created_at->format('Y/m/d H:i') }}</td>
                  <td>{{ $token->last_used_at?->format('Y/m/d H:i') ?? '未使用' }}</td>
                  <td>
                    <form
                      action="{{ route('mypage.tokens.destroy', $token->id) }}"
                      method="POST"
                      class="js-confirm"
                      data-text="トークン「{{ $token->name }}」を削除しますか？削除後は使用できなくなります。"
                    >
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="v2-button v2-button-danger v2-button-sm">削除</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">発行済みトークンはありません</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>


    </div>
  </div>
@endsection
