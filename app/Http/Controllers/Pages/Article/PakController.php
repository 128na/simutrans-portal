<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\LatestAction;
use App\Enums\CategoryType;
use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\CategoryRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class PakController extends Controller
{
    public function __construct(
        private readonly LatestAction $latestAction,
        private readonly CategoryRepository $categoryRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function latest(): View
    {
        return view('pages.pak.latest', [
            'pak128Japan' => ArticleList::collection($this->latestAction->byPak('128-japan', 12)),
            'pak128' => ArticleList::collection($this->latestAction->byPak('128', 12)),
            'pak64' => ArticleList::collection($this->latestAction->byPak('64', 12)),
            'meta' => $this->metaOgpService->frontLatest(),
        ]);
    }

    public function pak128jp(): View
    {
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '128-japan');

        return view('pages.pak.index', [
            'pak' => '128-japan',
            'categoryIds' => [$category->id],
            'articles' => ArticleList::collection($this->latestAction->byPak('128-japan')),
            'meta' => $this->metaOgpService->frontPak('128-japan'),
        ]);
    }

    public function pak128(): View
    {
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '128');

        return view('pages.pak.index', [
            'pak' => '128',
            'categoryIds' => [$category->id],
            'articles' => ArticleList::collection($this->latestAction->byPak('128')),
            'meta' => $this->metaOgpService->frontPak('128'),
        ]);
    }

    public function pak64(): View
    {
        $category = $this->categoryRepository->getByTypeSlug(CategoryType::Pak, '64');

        return view('pages.pak.index', [
            'pak' => '64',
            'categoryIds' => [$category->id],
            'articles' => ArticleList::collection($this->latestAction->byPak('64')),
            'meta' => $this->metaOgpService->frontPak('64'),
        ]);
    }

    public function pakOthers(): View
    {
        $categories = $this->categoryRepository->getByExcludeTypeSlug(CategoryType::Pak, ['128-japan', '128', '64']);

        return view('pages.pak.index', [
            'pak' => 'other-pak',
            'categoryIds' => $categories->pluck('id')->toArray(),
            'articles' => ArticleList::collection($this->latestAction->others()),
            'meta' => $this->metaOgpService->frontPak('others'),
        ]);
    }
}
