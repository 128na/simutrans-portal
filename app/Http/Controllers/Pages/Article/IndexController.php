<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\SearchAction;
use App\Http\Resources\Frontend\ArticleList;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class IndexController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function announces(): View
    {
        return view('pages.announces.index', [
            'articles' => ArticleList::collection($this->articleRepository->getAnnounces()),
            'meta' => $this->metaOgpService->frontAnnounces(),
        ]);
    }

    public function pages(): View
    {
        return view('pages.static.index', [
            'articles' => ArticleList::collection($this->articleRepository->getPages()),
            'meta' => $this->metaOgpService->frontPages(),
        ]);
    }

    public function search(Request $request, SearchAction $searchAction): View
    {
        $condition = $request->all();

        return view('pages.search.index', [
            ...$searchAction($condition),
            'meta' => $this->metaOgpService->frontSearch(),
        ]);
    }
}
