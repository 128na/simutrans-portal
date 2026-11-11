<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Lang;

/**
 * 記事詳細の現在位置（トップ / pak / 種類）を組み立てる.
 *
 * pak・種類が複数付いている記事は、カテゴリの表示順（order）で先頭のものを使う（pak は「Pak問わず」より具体的な pak を優先）。
 * 一般記事（page・markdown）はトップだけを示す。
 */
class ArticleBreadcrumbService
{
    /**
     * pak の slug ごとの一覧ページ。ここにない pak は「その他のpak」に寄せる.
     * 数字だけの slug（128・64）は PHP の配列キーでは int になる.
     *
     * @var array<int|string,string>
     */
    private const array PAK_ROUTES = [
        '128-japan' => 'pak.128japan',
        '128' => 'pak.128',
        '64' => 'pak.64',
    ];

    /**
     * @return list<array{title:string,url:string}>
     */
    public function forArticle(Article $article): array
    {
        $items = [['title' => 'トップ', 'url' => route('index')]];

        if (! in_array($article->post_type, [ArticlePostType::AddonPost, ArticlePostType::AddonIntroduction], true)) {
            return $items;
        }

        $categories = $article->categories->sortBy('order');
        $paks = $categories->filter(fn (Category $category): bool => $category->type === CategoryType::Pak);
        // 「Pak問わず」は表示順が先頭だが、具体的な pak も付いていればそちらを現在位置にする
        $pak = $paks->first(fn (Category $category): bool => $category->slug !== 'any') ?? $paks->first();
        $addon = $categories->first(fn (Category $category): bool => $category->type === CategoryType::Addon);

        if ($pak instanceof Category) {
            $items[] = [
                'title' => Lang::get('category.pak.'.$pak->slug),
                'url' => route(self::PAK_ROUTES[$pak->slug] ?? 'pak.others'),
            ];
        }

        if ($addon instanceof Category) {
            $items[] = [
                'title' => Lang::get('category.addon.'.$addon->slug),
                'url' => $pak instanceof Category
                    ? route('categories.pakAddon', ['pak' => $pak->slug, 'addon' => $addon->slug])
                    : route('search', ['categoryIds' => [$addon->id]]),
            ];
        }

        return $items;
    }
}
