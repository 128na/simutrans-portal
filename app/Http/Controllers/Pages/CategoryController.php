<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ArticleCard;
use App\Repositories\Article\FrontArticleRepository;
use App\Repositories\CategoryRepository;
use App\Services\Front\ArticleListFilter;
use App\Services\Front\MetaOgpService;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly FrontArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly MetaOgpService $metaOgpService,
        private readonly PublicArticleSnapshot $snapshot,
    ) {}

    /**
     * 件数は保存済みの表示データだけを読み、閲覧時に集計しない。保存データが使えないときは件数なしでリンクだけを出す
     */
    public function categories(): View
    {
        return view('pages.categories.index', [
            'pakSections' => $this->pakSections($this->snapshot->read()['pak_addon_counts']),
            'meta' => $this->metaOgpService->frontPakAddonList(),
        ]);
    }

    public function categoryPakAddon(Request $request, string $pakSlug, string $addonSlug): View
    {
        $filter = ArticleListFilter::fromRequest($request)->withoutAddon();
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, $pakSlug);
        $addon = $this->categoryRepository->getByTypeSlug(CategoryType::Addon, $addonSlug);

        return view('pages.categories.show', [
            'pak' => $category,
            'addon' => $addon,
            'articles' => ArticleCard::collection($this->articleRepository->getForPakAddon($category->id, $addon->id, filter: $filter)),
            'filter' => $filter,
            'meta' => $this->metaOgpService->frontPakAddon($category, $addon),
        ]);
    }

    /**
     * @param  array<array-key, array<string, array{total: int, addon_post: int}>>|null  $counts
     * @return list<array{label: string, addons: list<array{title: string, url: string}>}>
     */
    private function pakSections(?array $counts): array
    {
        /** @var array<string, string> $addonLabels */
        $addonLabels = trans('category.addon');
        unset($addonLabels['none']);

        $sections = [];
        foreach (PublicArticleSnapshot::PAKS as $pak) {
            $addons = $counts === null
                ? array_fill_keys(array_keys($addonLabels), null)
                : array_map(
                    fn (array $count): int => $count['total'],
                    array_filter(
                        array_intersect_key($counts[$pak] ?? [], $addonLabels),
                        fn (array $count): bool => $count['total'] > 0,
                    ),
                );
            $addons = array_replace(array_intersect_key($addonLabels, $addons), $addons);

            $links = [];
            foreach ($addons as $addon => $count) {
                $links[] = [
                    'title' => $count === null ? $addonLabels[$addon] : "{$addonLabels[$addon]} ({$count})",
                    'url' => route('categories.pakAddon', ['pak' => $pak, 'addon' => $addon]),
                ];
            }

            $sections[] = ['label' => __('category.pak.'.$pak), 'addons' => $links];
        }

        return $sections;
    }
}
