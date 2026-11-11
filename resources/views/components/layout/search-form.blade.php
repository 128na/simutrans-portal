{{-- キーワード検索フォーム（サイドメニュー・モバイルメニュー・ヘッダー共通）。/search?word=... へ送る --}}
@props(['inputId'])

<form method="GET" action="{{ route('search') }}" {{ $attributes }}>
  <div class="flex items-center gap-x-2 w-full">
    <label for="{{ $inputId }}" class="sr-only">キーワード</label>
    <input
      id="{{ $inputId }}"
      type="search"
      name="word"
      placeholder="キーワードを入力"
      class="flex-1 min-w-0 v2-input"
    />
    <button type="submit" class="v2-button v2-button-md v2-button-primary flex-shrink-0">検索</button>
  </div>
</form>
