<dl>
    <dt>作者 / 投稿者</dt>
    <dd>
        @if ($article->contents->author)
            {{ $article->contents->author }}</a> /
        @endif
        <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
    </dd>
    <dt>カテゴリ</dt>
    <dd>
        @include('parts.category-list', [
            'categories' => $article->categories,
            'post_type' => $article->post_type,
            'route_name' => 'addons.index',
        ])
    </dd>
    @if ($article->tags->isNotEmpty())
        <dt>タグ</dt>
        <dd>
            @include('parts.tag-list', ['tags' => $article->tags])
        </dd>
    @endif
    <dt>説明</dt>
    <dd>{{ $article->contents->description }}</dd>
    @if ($article->contents->thanks)
        <dt>謝辞・参考にしたアドオン</dt>
        <dd>{{ $article->contents->thanks }}</dd>
    @endif
    @if ($article->contents->license)
        <dt>ライセンス</dt>
        <dd>{{ $article->contents->license }}</dd>
    @endif
    <dt class="mb-2">ダウンロード</dt>
    <dd>
        <a class="btn btn-lg btn-primary js-download" href="{{ route('articles.download', $article) }}">ダウンロードする</a>
    </dd>
</dl>
