<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Mypage;

use App\Services\Front\MetaOgpService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\Analytics\FindArticles;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Api\Mypage\ArticleAnalytic;
use App\Repositories\v2\ArticleRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AnalyticsController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        return view('v2.mypage.analytics', [
            'articles' => $this->articleRepository->getForAnalyticsList($user),
            'meta' => $this->metaOgpService->analytics(),
        ]);
    }

    public function show(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        return ArticleAnalytic::collection(
            $findArticles($this->loggedinUser(), $searchRequest)
        );
    }
}
