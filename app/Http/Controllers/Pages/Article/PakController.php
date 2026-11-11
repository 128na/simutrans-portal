<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\LatestAction;
use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ArticleCard;
use App\Repositories\CategoryRepository;
use App\Services\Front\ArticleListFilter;
use App\Services\Front\MetaOgpService;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PakController extends Controller
{
    public function __construct(
        private readonly LatestAction $latestAction,
        private readonly CategoryRepository $categoryRepository,
        private readonly MetaOgpService $metaOgpService,
        private readonly PublicArticleSnapshot $snapshot,
    ) {}

    public function latest(): View
    {
        $latest = $this->snapshot->read()['latest'];

        return view('pages.pak.latest', [
            'pak128Japan' => $latest['128-japan'] ?? [],
            'pak128' => $latest['128'] ?? [],
            'pak64' => $latest['64'] ?? [],
            'meta' => $this->metaOgpService->frontLatest(),
        ]);
    }

    public function pak128jp(Request $request): View
    {
        $filter = ArticleListFilter::fromRequest($request);
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '128-japan');

        return view('pages.pak.index', [
            'pak' => '128-japan',
            'categoryIds' => [$category->id],
            'articles' => ArticleCard::collection($this->latestAction->byPak('128-japan', filter: $filter)),
            'filter' => $filter,
            'addonCounts' => $this->snapshot->addonCounts('128-japan', $filter->downloadOnly),
            'meta' => $this->metaOgpService->frontPak('128-japan'),
        ]);
    }

    public function pak128(Request $request): View
    {
        $filter = ArticleListFilter::fromRequest($request);
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '128');

        return view('pages.pak.index', [
            'pak' => '128',
            'categoryIds' => [$category->id],
            'articles' => ArticleCard::collection($this->latestAction->byPak('128', filter: $filter)),
            'filter' => $filter,
            'addonCounts' => $this->snapshot->addonCounts('128', $filter->downloadOnly),
            'meta' => $this->metaOgpService->frontPak('128'),
        ]);
    }

    public function pak64(Request $request): View
    {
        $filter = ArticleListFilter::fromRequest($request);
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '64');

        return view('pages.pak.index', [
            'pak' => '64',
            'categoryIds' => [$category->id],
            'articles' => ArticleCard::collection($this->latestAction->byPak('64', filter: $filter)),
            'filter' => $filter,
            'addonCounts' => $this->snapshot->addonCounts('64', $filter->downloadOnly),
            'meta' => $this->metaOgpService->frontPak('64'),
        ]);
    }

    public function pakOthers(Request $request): View
    {
        $filter = ArticleListFilter::fromRequest($request);
        $categories = $this->categoryRepository->getByExcludeTypeSlug(CategoryType::Pak, ['128-japan', '128', '64']);

        return view('pages.pak.index', [
            'pak' => 'other-pak',
            'categoryIds' => $categories->pluck('id')->toArray(),
            'articles' => ArticleCard::collection($this->latestAction->others(filter: $filter)),
            'filter' => $filter,
            'meta' => $this->metaOgpService->frontPak('others'),
        ]);
    }
}
