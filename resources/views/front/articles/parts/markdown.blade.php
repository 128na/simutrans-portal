<section>
    @markdown($article->contents->markdown)
</section>

@if ($article->categories->isNotEmpty())
    <dl>
        <dt>カテゴリ</dt>
        <dd>
            @include('parts.category-list', ['categories' => $article->categories,
                'post_type' => $article->isAnnounce() ? null : $article->post_type, 'route_name' => 'pages.index'])
        </dd>
    </dl>
@endif
