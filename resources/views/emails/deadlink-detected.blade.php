<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    {{ __('email.deadlink-detected.message_1', ['title' => $article->title]) }}
</p>
<p>
    {{ __('email.deadlink-detected.message_2') }}
</p>
<p>
    {{ __('email.deadlink-detected.message_3') }}
    <a href="{{ route('mypage.articles.edit', $article) }}">{{ route('mypage.articles.edit', $article) }}</a>
</p>
