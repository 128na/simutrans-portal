<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enums\CategoryType;
use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
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

    public function categoryPakAddon(string $pakSlug, string $addonSlug): View
    {
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, $pakSlug);
        $addon = $this->categoryRepository->getByTypeSlug(CategoryType::Addon, $addonSlug);

        return view('pages.categories.show', [
            'pak' => $category,
            'addon' => $addon,
            'articles' => ArticleList::collection($this->articleRepository->getForPakAddon($category->id, $addon->id)),
            'meta' => $this->metaOgpService->frontPakAddon($category, $addon),
        ]);
    }
}
