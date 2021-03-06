<?php

namespace App\Http\View\Creators;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\View\View;

/**
 * タイトル、メタ、OGP、Schemaデータを設定する.
 */
class MetaCreator
{
    private string $name;
    private array $data;
    private SchemaService $schema_service;

    public function __construct(SchemaService $schema_service)
    {
        $this->schema_service = $schema_service;
    }

    /**
     * データをビューと結合.
     *
     * @return void
     */
    public function create(View $view)
    {
        $this->name = $view->getName();
        $this->data = $view->getData();

        $view->with($this->handleTypes());
    }

    private function handleTypes()
    {
        $route = request()->route();
        switch (true) {
            case $this->name === 'front.index':
                return $this->forTop();

            case $route->named('addons.index'):
                return $this->forList('記事一覧');
            case $route->named('addons.ranking'):
                return $this->forList('アクセスランキング');
            case $route->named('pages.index'):
                return $this->forList('一般記事一覧');
            case $route->named('announces.index'):
                return $this->forList('お知らせ一覧');

            case $route->named('category'):
                return $this->forCategory();
            case $route->named('category.pak.addon'):
                return $this->forPakAddon();
            case $route->named('tag'):
                return $this->forTag();
            case $route->named('user'):
                return $this->forUser();
            case $route->named('search'):
                return $this->forSearch();
            case $route->named('advancedSearch'):
                return $this->forAdvancedSearch();

            case $this->name === 'front.public-bookmarks.index':
                return $this->forPublicBookmarkList();

            case $this->name === 'front.public-bookmarks.show':
                return $this->forPublicBookmark();

            case $this->name === 'front.tags':
                return $this->forTags();

            case $this->name === 'front.articles.show':
                return $this->forShow();

            logger('unknown route:'.$route);
        }
    }

    /**
     * 公開ブックマーク一覧.
     */
    public function forPublicBookmarkList()
    {
        return [
            'title' => '公開ブックマーク一覧',
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => '公開ブックマーク一覧'],
            ],
        ];
    }

    /**
     * 公開ブックマーク.
     */
    public function forPublicBookmark()
    {
        return [
            'title' => $this->data['item']->title,
        ];
    }

    /**
     * トップページ用.
     */
    public function forTop()
    {
        return [
            'title' => 'トップ',
            'schemas' => $this->schema_service->forTop(),
        ];
    }

    /**
     * アドオン新着、ランキング、お知らせ、一般記事記事一覧ページ用.
     */
    public function forList($name)
    {
        return [
            'title' => $name,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $name],
            ],
        ];
    }

    /**
     * カテゴリの記事一覧ページ用.
     */
    public function forCategory()
    {
        $category = $this->data['category'];
        $title = __("category.{$category->type}.{$category->slug}").'カテゴリ';

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $title, 'bookmarkItemableType' => Category::class, 'bookmarkItemableId' => $category->id],
            ],
        ];
    }

    /**
     * pak別カテゴリの記事一覧ページ用.
     */
    public function forPakAddon()
    {
        $pak = $this->data['categories']['pak'];
        $addon = $this->data['categories']['addon'];
        $pak_title = __('category.pak.'.$pak->slug);
        $addon_title = __('category.addon.'.$addon->slug);
        $title = "{$pak_title}/{$addon_title}カテゴリ";

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $pak_title, 'bookmarkItemableType' => Category::class, 'bookmarkItemableId' => $pak->id, 'url' => route('category', ['pak', $pak->slug])],
                ['name' => $addon_title, 'bookmarkItemableType' => Category::class, 'bookmarkItemableId' => $addon->id],
            ],
            'open_menu_pak_addon' => [$pak->slug => true],
        ];
    }

    /**
     * 記事詳細ページ用.
     */
    public function forShow()
    {
        $article = $this->data['article'];

        return [
            'title' => $article->title,
            'canonical_url' => route('articles.show', $article->slug),
            'schemas' => $this->schema_service->forShow($article),
        ];
    }

    /**
     * タグの記事一覧ページ用.
     */
    public function forTag()
    {
        $tag = $this->data['tag'];
        $title = "{$tag->name}タグ";

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => 'タグ一覧', 'url' => route('tags')],
                ['name' => $title, 'bookmarkItemableType' => Tag::class, 'bookmarkItemableId' => $tag->id],
            ],
        ];
    }

    /**
     *  ユーザー記事一覧ページ用.
     */
    public function forUser()
    {
        $user = $this->data['user'];
        $title = $user->name.'さんの投稿一覧';

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $title, 'bookmarkItemableType' => User::class, 'bookmarkItemableId' => $user->id],
            ],
            'user' => $user->load('profile', 'profile.attachments'),
            'open_menu_user_addon' => true,
        ];
    }

    /**
     *  検索記事一覧ページ用.
     */
    public function forSearch()
    {
        $request = $this->data['request'];
        $title = "「{$request->word}」での検索結果";

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $title],
            ],
            'word' => $request->word,
        ];
    }

    /**
     *  検索記事一覧ページ用.
     */
    public function forAdvancedSearch()
    {
        $title = '詳細検索';

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $title],
            ],
        ];
    }

    /**
     *  タグ一覧ページ用.
     */
    public function forTags()
    {
        $title = 'タグ一覧';

        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => 'トップ', 'url' => route('index')],
                ['name' => $title],
            ],
        ];
    }
}
