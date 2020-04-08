<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Api\Mypage\ArticleAnalytics as ArticleAnalyticsResource;
use App\Models\Article;
use App\Services\ArticleAnalyticsService;
use Illuminate\Support\Facades\Auth;

class ArticleAnalyticsController extends Controller
{
    /**
     * @var ArticleAnalyticsService
     */
    private $article_analytics_service;

    public function __construct(ArticleAnalyticsService $article_analytics_service)
    {
        $this->article_analytics_service = $article_analytics_service;
    }

    public function index(SearchRequest $request)
    {
        return new ArticleAnalyticsResource(
            $this->article_analytics_service->getArticles(Auth::user(), $request)
        );
    }

}
