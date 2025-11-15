<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Http\Resources\ArticleAnalytic;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        return ArticleAnalytic::collection($findArticles($user, $searchRequest));
    }
}
