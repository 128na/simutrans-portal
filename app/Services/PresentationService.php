<?php

namespace App\Services;

use App\Http\Requests\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\PakAddonCount;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserAddonCount;

/**
 * タイトルやパンなど画面表示用のコンテンツを扱う
 */
class PresentationService extends Service
{
    /**
     * @var PakAddonCount
     */
    private $pak_addon_count;
    /**
     * @var UserAddonCount
     */
    private $user_addon_count;
    /**
     * @var SchemaService
     */
    private $schema_service;

    public function __construct(
        PakAddonCount $pak_addon_count,
        UserAddonCount $user_addon_count,
        SchemaService $schema_service
    ) {

        $this->pak_addon_count = $pak_addon_count;
        $this->user_addon_count = $user_addon_count;
        $this->schema_service = $schema_service;
    }

    /**
     * 記事詳細ページ用
     */
    public function forShow(Article $article)
    {
        return $this->withHeaderContents([
            'title' => __($article->title),
            'canonical_url' => route('articles.show', $article->slug),
            'schemas' => $this->schema_service->forShow($article),
        ]);
    }
    /**
     * トップページ用
     */
    public function forTop()
    {
        return $this->withHeaderContents([
            'title' => __('Top'),
            'schemas' => $this->schema_service->forTop(),
        ]);
    }

    /**
     * アドオン新着、ランキング、お知らせ、一般記事記事一覧ページ用
     */
    public function forList($name, $articles)
    {
        return $this->withHeaderContents([
            'title' => __($name),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __($name)],
            ],
            'schemas' => $this->schema_service->forList($name, $articles),
        ]);
    }
    /**
     * タグの記事一覧ページ用
     */
    public function forTag(Tag $tag, $articles)
    {
        $title = $tag->name;
        return $this->withHeaderContents([
            'title' => __('Tag :name', ['name' => $title]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'schemas' => $this->schema_service->forList($title, $articles),
        ]);
    }
    /**
     * カテゴリの記事一覧ページ用
     */
    public function forCategory(Category $category, $articles)
    {
        $title = __("category.{$category->type}.{$category->slug}");
        return $this->withHeaderContents([
            'title' => __('Category :name', ['name' => $title]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'schemas' => $this->schema_service->forList($title, $articles),
        ]);
    }
    /**
     * pak別カテゴリの記事一覧ページ用
     */
    public function forPakAddon(Category $pak, Category $addon, $articles)
    {
        $title = __(':pak, :addon', [
            'pak' => __('category.pak.' . $pak->slug),
            'addon' => __('category.addon.' . $addon->slug),
        ]);
        return $this->withHeaderContents([
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('category.pak.' . $pak->slug), 'url' => route('category', ['pak', $pak->slug])],
                ['name' => __('category.addon.' . $addon->slug)],
            ],
            'open_menu_pak_addon' => [$pak->slug => true],
            'schemas' => $this->schema_service->forList($title, $articles),
        ]);
    }
    /**
     *  ユーザー記事一覧ページ用
     */
    public function forUser(User $user, $articles)
    {
        $title = __('User :name', ['name' => $user->name]);
        return $this->withHeaderContents([
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'user' => $user->load('profile', 'profile.attachments'),
            'open_menu_user_addon' => true,
            'schemas' => $this->schema_service->forList($title, $articles),
        ]);
    }
    /**
     *  検索記事一覧ページ用
     */
    public function forSearch(SearchRequest $request, $articles)
    {
        $title = __('Search results by :word', ['word' => $request->word]);
        return $this->withHeaderContents([
            'title' => $title,
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $title],
            ],
            'word' => $request->word,
            'schemas' => $this->schema_service->forList($title, $articles),
        ]);
    }

    /**
     * コンテンツにユーザー別アドオン投稿数一覧、pak別アドオン投稿数一覧をマージする
     */
    private function withHeaderContents(array $contents): array
    {
        return array_merge($contents,
            [
                'menu_user_addon_counts' => $this->getUserAddonCounts(),
                'menu_pak_addon_counts' => $this->getPakAddonCounts(),
            ]);
    }

    /**
     * ユーザー別アドオン投稿数一覧
     */
    private function getUserAddonCounts()
    {
        return $this->user_addon_count->select('user_id', 'user_name', 'count')->get();
    }
    /**
     * pak別アドオン投稿数一覧
     */
    private function getPakAddonCounts()
    {
        return $this->separateByPak(
            $this->pak_addon_count->select('pak_slug', 'addon_slug', 'count')->get()
        );
    }

    private function separateByPak($pak_addon_counts)
    {
        return collect($pak_addon_counts->reduce(function ($separated, $item) {
            if (!isset($separated[$item->pak_slug])) {
                $separated[$item->pak_slug] = collect([]);
            }
            $separated[$item->pak_slug]->push($item);
            return $separated;
        }, []));
    }
}
