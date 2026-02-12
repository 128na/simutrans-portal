@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">招待</h2>
      <p class="text-c-sub">
        招待リンクの発行と招待したユーザーを確認できます。
      </p>
    </div>
    <div class="v2-page-content-area-lg">
      <div>
        <h4 class="v2-text-h3 mb-4">招待リンク</h4>
        <div>
          <form
            id="revoke"
            action="{{ route('mypage.invite') }}"
            method="POST"
            class="js-confirm"
            data-text="招待リンクを削除しますか？"
          >
            @csrf
            @method('DELETE')
          </form>
          <form
            id="generate"
            action="{{ route('mypage.invite') }}"
            method="POST"
          >
            @csrf

            @if ($user->invitation_code)
              <input
                type="text"
                disabled
                value="{{ route('user.invite', $user->invitation_code) }}"
                class="v2-input w-full mb-4"
              />
              <div class="gap-x-2 flex">
                <button
                  type="submit"
                  class="v2-button v2-button-lg v2-button-sub js-clipboard"
                  data-text="{{ route('user.invite', $user->invitation_code) }}"
                >
                  リンクをコピー
                </button>
                <button
                  type="submit"
                  class="v2-button v2-button-lg v2-button-primary"
                >
                  再発行
                </button>
                <button
                  type="submit"
                  class="v2-button v2-button-lg v2-button-danger"
                  form="revoke"
                >
                  削除
                </button>
              </div>
            @else
              <button
                type="submit"
                class="v2-button v2-button-lg v2-button-primary"
              >
                発行
              </button>
            @endif
          </form>
        </div>
      </div>

      <div>
        <h4 class="v2-text-h3 mb-4">招待履歴</h4>
        <div class="v2-table-wrapper">
          <table class="v2-table">
            <thead>
              <tr>
                <th>日時</th>
                <th>ユーザー名</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($user->invites as $invite)
                <tr>
                  <td>{{ $invite->created_at->format('Y/m/d H:i:s') }}</td>
                  <td>{{ $invite->name }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2">履歴はありません</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
