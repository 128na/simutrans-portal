@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h2 mb-2">APIトークン</h2>
      <p class="text-c-sub">
        MCPサーバーや外部APIから自分のアカウントにアクセスするためのトークンを管理します。
      </p>
    </div>

    @if (session('new_token'))
      <div class="mb-8 v2-card v2-card-warning">
        <p class="font-bold mb-2">新しいAPIトークンが発行されました</p>
        <p class="text-sm mb-3 text-c-sub">このトークンは一度しか表示されません。必ずコピーして安全な場所に保管してください。</p>
        <div class="flex gap-2 items-center">
          <input
            type="text"
            readonly
            value="{{ session('new_token') }}"
            class="v2-input flex-1 font-mono text-sm"
            id="new-token-value"
          />
          <button
            type="button"
            class="v2-button v2-button-sub js-clipboard shrink-0"
            data-text="{{ session('new_token') }}"
          >
            コピー
          </button>
        </div>
      </div>
    @endif

    @if (session('status'))
      <div class="mb-6 v2-card v2-card-primary">
        <p>{{ session('status') }}</p>
      </div>
    @endif

    <div class="v2-page-content-area-lg">
      <div>
        <h4 class="v2-text-h3 mb-4">新しいトークンを発行</h4>
        <form action="{{ route('mypage.tokens.store') }}" method="POST" class="flex gap-2 items-end">
          @csrf
          <div class="flex-1">
            <label for="token-name" class="block text-sm mb-1">トークン名（用途が分かる名前）</label>
            <input
              type="text"
              id="token-name"
              name="name"
              placeholder="例：Claude Code MCP"
              maxlength="255"
              required
              class="v2-input w-full"
              value="{{ old('name') }}"
            />
            @error('name')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
          <button type="submit" class="v2-button v2-button-primary shrink-0">発行</button>
        </form>
      </div>

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

      <div>
        <h4 class="v2-text-h3 mb-4">MCPサーバーへの接続設定</h4>
        <p class="text-sm text-c-sub mb-3">Claude Desktopなどのクライアントで以下のように設定します。</p>
        <pre class="v2-card bg-gray-900 text-gray-100 text-sm overflow-x-auto p-4 rounded"><code>{
  "mcpServers": {
    "simutrans-portal-user": {
      "url": "{{ config('app.url') }}/mcp/user",
      "headers": {
        "Authorization": "Bearer &lt;発行したトークン&gt;"
      }
    }
  }
}</code></pre>
      </div>
    </div>
  </div>
@endsection
