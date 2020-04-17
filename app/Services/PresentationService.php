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

    public function __construct(
        PakAddonCount $pak_addon_count,
        UserAddonCount $user_addon_count
    ) {

        $this->pak_addon_count = $pak_addon_count;
        $this->user_addon_count = $user_addon_count;
    }

    /**
     * 記事詳細ページ用
     */
    public function forShow(Article $article)
    {
        return $this->withHeaderContents([
            'title' => __($article->title),
            'canonical_url' => route('articles.show', $article->slug),
        ]);
    }
    /**
     * トップページ用
     */
    public function forTop()
    {
        return $this->withHeaderContents([
            'title' => __('Top'),
        ]);
    }

    /**
     * アドオン新着、ランキング、お知らせ、一般記事記事一覧ページ用
     */
    public function forList($name)
    {
        return $this->withHeaderContents([
            'title' => __($name),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __($name)],
            ],
        ]);
    }
    /**
     * タグの記事一覧ページ用
     */
    public function forTag(Tag $tag)
    {
        return $this->withHeaderContents([
            'title' => __('Tag :name', ['name' => $tag->name]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => $tag->name],
            ],
        ]);
    }
    /**
     * カテゴリの記事一覧ページ用
     */
    public function forCategory(Category $category)
    {
        return $this->withHeaderContents([
            'title' => __('Category :name', ['name' => __("category.{$category->type}.{$category->slug}")]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __("category.{$category->type}.{$category->slug}")],
            ],
        ]);
    }
    /**
     * pak別カテゴリの記事一覧ページ用
     */
    public function forPakAddon(Category $pak, Category $addon)
    {
        return $this->withHeaderContents([
            'title' => __(':pak, :addon', [
                'pak' => __('category.pak.' . $pak->slug),
                'addon' => __('category.addon.' . $addon->slug),
            ]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('category.pak.' . $pak->slug), 'url' => route('category', ['pak', $pak->slug])],
                ['name' => __('category.addon.' . $addon->slug)],
            ],
            'open_menu_pak_addon' => [$pak->slug => true],
        ]);
    }
    /**
     *  ユーザー記事一覧ページ用
     */
    public function forUser(User $user)
    {
        return $this->withHeaderContents([
            'title' => __('User :name', ['name' => $user->name]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('User :name', ['name' => $user->name])],
            ],
            'user' => $user->load('profile', 'profile.attachments'),
            'open_menu_user_addon' => true,
        ]);
    }
    /**
     *  検索記事一覧ページ用
     */
    public function forSearch(SearchRequest $request)
    {
        return $this->withHeaderContents([
            'title' => __('Search results by :word', ['word' => $request->word]),
            'breadcrumb' => [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('Search results by :word', ['word' => $request->word])],
            ],
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
        return UserAddonCount::all();
    }
    /**
     * pak別アドオン投稿数一覧
     */
    private function getPakAddonCounts()
    {
        return $this->separateByPak(PakAddonCount::all());
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
