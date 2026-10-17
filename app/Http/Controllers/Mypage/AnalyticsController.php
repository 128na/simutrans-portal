<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Mypage\ArticleAnalytic;
use App\Repositories\Article\MypageArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.analytics', [
            'articles' => $this->mypageArticleRepository->getForAnalyticsList($user),
            'meta' => $this->metaOgpService->mypageAnalytics(),
        ]);
    }

    /**
     * アナリティクスデータを取得
     */
    public function show(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        $user = $this->loggedinUser();

        return ArticleAnalytic::collection(
            $findArticles($user, $searchRequest)
        );
    }
}
