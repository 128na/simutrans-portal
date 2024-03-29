<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Api\Mypage\ArticleAnalytics as ArticleAnalyticsResource;
use App\Services\ArticleAnalyticsService;

class AnalyticsController extends Controller
{
    public function __construct(private readonly ArticleAnalyticsService $articleAnalyticsService)
    {
    }

    public function index(SearchRequest $searchRequest): ArticleAnalyticsResource
    {
        return new ArticleAnalyticsResource(
            $this->articleAnalyticsService->findArticles($this->loggedinUser(), $searchRequest)
        );
    }
}
