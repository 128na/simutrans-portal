@include('emails.header', ['name' => $user->name])

<p>
    「{{ $article->title }}」のダウンロード先URLがリンク切れになっていることを検出しました。
</p>
<p>
    記事は自動的に非公開に変更されました。リンク先が変更になった場合は記事の修正をお願いします。<br>
    自動リンク切れチェックは記事編集から無効化できます。
</p>
<p>
    マイページ：
    <a href="{{ route('mypage.index') }}">{{ route('mypage.index') }}</a>
</p>
@include('emails.footer')
