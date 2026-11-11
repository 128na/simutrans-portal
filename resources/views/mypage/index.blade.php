@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-8 flex flex-wrap items-center gap-3">
      <h2 class="v2-text-h2">マイページ</h2>
      <a
        href="{{ route('mypage.articles.create') }}"
        class="v2-button v2-button-lg v2-button-primary ml-auto font-semibold"
      >
        アドオンを投稿する
      </a>
    </div>
    <div class="v2-page-content-area-lg">
      @if (! $user->hasVerifiedEmail())
        <div class="v2-card v2-card-warn flex flex-wrap items-center gap-x-3 gap-y-1" role="status">
          <span class="font-semibold">メールアドレスの認証が完了していません</span>
          <span class="text-c-main">記事の投稿などを使うには認証が必要です。</span>
          <a href="{{ route('mypage.verify-email') }}" class="v2-link ml-auto font-semibold">認証する</a>
        </div>
      @endif

      @if ($showTwoFactorNotice)
        <div class="v2-card v2-card-warn flex flex-wrap items-center gap-x-3 gap-y-1" role="status">
          <span class="font-semibold">二要素認証が無効です</span>
          <span class="text-c-main">アカウントを守るために設定をおすすめします。</span>
          <a href="{{ route('mypage.two-factor') }}" class="v2-link ml-auto font-semibold">設定する</a>
        </div>
      @endif

      <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="v2-card flex flex-col gap-1 border-c-border">
          <dt class="text-note text-c-sub">今月の表示回数（PV）</dt>
          <dd class="text-3xl font-bold tabular-nums">{{ number_format((int) ($summary->total_view_count ?? 0)) }}</dd>
        </div>
        <div class="v2-card flex flex-col gap-1 border-c-border">
          <dt class="text-note text-c-sub">今月のダウンロード（CV）</dt>
          <dd class="text-3xl font-bold tabular-nums">
            {{ number_format((int) ($summary->total_conversion_count ?? 0)) }}
          </dd>
        </div>
        <div class="v2-card flex flex-col gap-1 border-c-border">
          <dt class="text-note text-c-sub">公開中の記事</dt>
          <dd class="text-3xl font-bold tabular-nums">
            {{ number_format((int) ($summary->published_article_count ?? 0)) }}
            <span class="text-note font-normal text-c-sub">
              / 全 {{ number_format((int) ($summary->article_count ?? 0)) }} 件
            </span>
          </dd>
        </div>
        <div class="v2-card flex flex-col gap-1 border-c-border">
          <dt class="text-note text-c-sub">ファイル使用量</dt>
          <dd class="text-3xl font-bold tabular-nums">
            {{ round(($summary->total_attachment_size ?? 0) / 1024 / 1024, 1) }}
            <span class="text-body font-normal">MB</span>
          </dd>
        </div>
      </dl>

      <section class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
          <h3 class="v2-text-h3">直近30日の推移</h3>
          <span class="text-note text-c-sub">最近更新した記事 {{ $recentArticles->count() }} 件の合計</span>
          <a href="{{ route('mypage.analytics') }}" class="v2-link ml-auto text-note font-semibold">
            アナリティクスで詳しく見る
          </a>
        </div>
        <script id="data-trend" type="application/json">
          @json($trend)
        </script>
        <div id="app-mypage-trend">読み込み中...</div>
      </section>

      <section class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
          <h3 class="v2-text-h3">最近更新した記事</h3>
          <a href="{{ route('mypage.articles.index') }}" class="v2-link ml-auto text-note font-semibold">
            すべての記事
          </a>
        </div>
        @if ($recentArticles->isEmpty())
          <p class="text-c-sub">まだ記事がありません。</p>
        @else
          <div class="v2-table-wrapper">
            <table class="v2-table w-full min-w-[640px]">
              <thead>
                <tr>
                  <th>タイトル</th>
                  <th>状態</th>
                  <th>更新日</th>
                  <th class="text-right">累計PV</th>
                  <th class="text-right">累計CV</th>
                  <th><span class="sr-only">操作</span></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($recentArticles as $article)
                  <tr>
                    <td>
                      @if ($article->status === \App\Enums\ArticleStatus::Publish)
                        <a href="{{ $article->showUrl() }}" class="v2-link font-semibold">{{ $article->title }}</a>
                      @else
                        <span class="font-semibold">{{ $article->title }}</span>
                      @endif
                    </td>
                    <td>
                      <span
                        @class([
                          'v2-badge whitespace-nowrap',
                          'v2-badge-success' => $article->status === \App\Enums\ArticleStatus::Publish,
                          'v2-badge-info' => $article->status === \App\Enums\ArticleStatus::Reservation,
                          'v2-badge-warn' => $article->status === \App\Enums\ArticleStatus::Draft,
                          'v2-badge-sub' => in_array($article->status, [\App\Enums\ArticleStatus::Private, \App\Enums\ArticleStatus::Trash], true),
                        ])
                      >
                        @lang("statuses.{$article->status->value}")
                      </span>
                    </td>
                    <td class="whitespace-nowrap text-c-sub">{{ $article->modified_at?->format('Y/m/d') }}</td>
                    <td class="text-right tabular-nums">
                      {{ number_format((int) ($article->totalViewCount?->count ?? 0)) }}
                    </td>
                    <td class="text-right tabular-nums">
                      {{ number_format((int) ($article->totalConversionCount?->count ?? 0)) }}
                    </td>
                    <td class="whitespace-nowrap text-right">
                      <a href="{{ route('mypage.articles.edit', $article) }}" class="v2-link font-semibold">編集</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    </div>
  </div>
@endsection
