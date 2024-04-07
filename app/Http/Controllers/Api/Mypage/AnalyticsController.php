<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Api\Mypage\ArticleAnalytic;
use App\Services\ArticleAnalyticsService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AnalyticsController extends Controller
{
    public function __construct(private readonly ArticleAnalyticsService $articleAnalyticsService)
    {
    }

    public function index(SearchRequest $searchRequest): AnonymousResourceCollection
    {
        return ArticleAnalytic::collection(
            $this->articleAnalyticsService->findArticles($this->loggedinUser(), $searchRequest)
        );
    }
}
