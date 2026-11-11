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
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly FrontArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function categories(): View
    {
        return view('pages.categories.index', [
            'pakAddonCategories' => $this->categoryRepository->getForPakAddonList(),
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
}
