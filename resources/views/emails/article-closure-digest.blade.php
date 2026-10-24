@include('emails.header', ['name' => $user->name])

<p>下記の{{ $items->count() }}件の記事を、記載日時に非公開にしました。</p>
<p>処理日時：{{ $run->finalized_at->format('Y/m/d H:i:s') }}</p>
<p>これは非公開化した時点の記録です。現在の記事の公開状態とは異なる場合があります。</p>

@foreach ($items as $item)
  <h2>{{ $item->title }}</h2>
  <p>掲載先URL：{{ $item->url }}</p>
  <p>非公開化日時：{{ $item->closed_at->format('Y/m/d H:i:s') }}</p>
  @if ($item->reason === \App\Enums\ArticleClosureReason::ServiceClosed)
    <p>理由：配布先サービスの終了</p>
  @else
    <p>理由：通常のリンク切れチェックでリンク切れを検出</p>
  @endif
  <p><a href="{{ $item->edit_url }}">記事を編集する</a></p>
@endforeach

<p>新しい配布先へ掲載URLを更新するか、配布に必要な権利・許可を確認して投稿記事へ変換した後、内容を確認して再公開できます。</p>
@if ($items->contains(fn ($item) => $item->reason === \App\Enums\ArticleClosureReason::DeadLink))
  <p>通常の自動リンク切れチェックは記事編集から無効化できます。</p>
@endif

@include('emails.footer')

