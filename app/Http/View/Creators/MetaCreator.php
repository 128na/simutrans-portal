<?php

namespace App\Http\View\Creators;

use App\Repositories\UserRepository;
use Illuminate\View\View;
use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\PakAddonCount;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserAddonCount;
use App\Services\SchemaService;

/**
 * タイトル、メタ、OGP、Schemaデータを設定する
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
     * データをビューと結合
     *
     * @param  View  $view
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
                return $this->forList('Articles');
            case $route->named('addons.ranking'):
                return $this->forList('Access Ranking');
            case $route->named('pages.index'):
                return $this->forList('Pages');
            case $route->named('announces.index'):
                return $this->forList('Announces');

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

            case $this->name === 'front.tags':
                return $this->forTags();

            case $this->name === 'front.articles.show':
                return $this->forShow();
        }
    }

    /**
     * トップページ用
     */
    public function forTop()
    {
        return [
            'title' => __('Top'),
            'schemas' => $this->schema_service->forTop(),
        ];
    }

    /**
     * アドオン新着、ランキング、お知らせ、一般記事記事一覧ページ用
     */
    public function forList($name)
    {
        return [
            'title' => __($name),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __($name)],
            ],
        ];
    }

    /**
     * カテゴリの記事一覧ページ用
     */
    public function forCategory()
    {
        $category = $this->data['category'];
        $title = __("category.{$category->type}.{$category->slug}");
        return [
            'title' => __('Category :name', ['name' => $title]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
        ];
    }

    /**
     * pak別カテゴリの記事一覧ページ用
     */
    public function forPakAddon()
    {
        $pak = $this->data['categories']['pak'];
        $addon = $this->data['categories']['addon'];
        $title = __(':pak, :addon', [
            'pak' => __('category.pak.' . $pak->slug),
            'addon' => __('category.addon.' . $addon->slug),
        ]);
        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('category.pak.' . $pak->slug), 'url' => route('category', ['pak', $pak->slug])],
                ['name' => __('category.addon.' . $addon->slug)],
            ],
            'open_menu_pak_addon' => [$pak->slug => true],
        ];
    }

    /**
     * 記事詳細ページ用
     */
    public function forShow()
    {
        $article = $this->data['article'];
        return [
            'title' => __($article->title),
            'canonical_url' => route('articles.show', $article->slug),
            'schemas' => $this->schema_service->forShow($article),
        ];
    }

    /**
     * タグの記事一覧ページ用
     */
    public function forTag()
    {
        $tag = $this->data['tag'];
        $title = $tag->name;
        return [
            'title' => __('Tag :name', ['name' => $title]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('Tags'), 'url' => route('tags')],
                ['name' => $title],
            ],
        ];
    }
    /**
     *  ユーザー記事一覧ページ用
     */
    public function forUser()
    {
        $user = $this->data['user'];
        $title = __('User :name', ['name' => $user->name]);
        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'user' => $user->load('profile', 'profile.attachments'),
            'open_menu_user_addon' => true,
        ];
    }

    /**
     *  検索記事一覧ページ用
     */
    public function forSearch()
    {
        $request = $this->data['request'];
        $title = __('Search results by :word', ['word' => $request->word]);
        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'word' => $request->word,
        ];
    }

    /**
     *  タグ一覧ページ用
     */
    public function forTags()
    {
        $title = __('Tags');
        return [
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'schemas' => $this->schema_service->forList($title),
        ];
    }
}
