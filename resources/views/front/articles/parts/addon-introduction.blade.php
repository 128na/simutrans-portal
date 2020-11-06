<dl>
    <dt>作者 / 投稿者</dt>
    <dd>
        {{ $article->contents->author }} / <a href="{{ route('user', [$article->user]) }}" rel="author">{{ $article->user->name }}</a>
    </dd>
    <dt>カテゴリ</dt>
    <dd>
        @include('parts.category-list', ['categories' => $article->categories,
            'post_type' => $article->post_type, 'route_name' => 'addons.index'])
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
    @if ($article->contents->agreement)
        <dt>掲載許可</dt>
        <dd>この記事は作者の許可を得てまたは作者自身により掲載しています。</dd>
    @endif
    <dt>掲載先URL</dt>
    <dd>
        <a
            href="{{$article->contents->link}}"
            data-url="{{ $article->contents->link }}"
            data-slug="{{ $article->slug }}"
            class="js-click text-primary"
            target="_blank"
            rel="noopener noreferrer"
        >{{ $article->contents->link }}</a>
    </dd>
</dl>
