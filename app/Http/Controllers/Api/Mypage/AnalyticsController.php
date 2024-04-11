<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Api\Mypage\ArticleAnalytic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AnalyticsController extends Controller
{
    public function index(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        return ArticleAnalytic::collection(
            $findArticles($this->loggedinUser(), $searchRequest)
        );
    }
}
