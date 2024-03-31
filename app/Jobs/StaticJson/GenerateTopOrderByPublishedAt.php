<?php

declare(strict_types=1);

namespace App\Jobs\StaticJson;

use App\Enums\CategoryType;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PrArticleResource;
use App\Services\Front\ArticleService;

class GenerateTopOrderByPublishedAt extends BaseGenerator
{
    protected function getJsonData(): array
    {
        /** @var ArticleService */
        $service = app(ArticleService::class);
        $pr = $service->prArticle();

        return [
            'paks' => [
                'pak128japan' => ArticleResource::collection($service->paginateByCategory(CategoryType::Pak, '128-japan', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'pak128' => ArticleResource::collection($service->paginateByCategory(CategoryType::Pak, '128', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'pak64' => ArticleResource::collection($service->paginateByCategory(CategoryType::Pak, '64', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'rankings' => ArticleResource::collection($service->paginateRanking(true)),
                'pages' => ArticleResource::collection($service->paginatePages(true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'announces' => ArticleResource::collection($service->paginateAnnouces(true, ArticleService::ORDER_BY_PUBLISHED_AT)),
            ],
            'pr' => $pr ? new PrArticleResource($pr) : null,
        ];
    }

    protected function getJsonName(): string
    {
        return 'top.published_at.json';
    }
}
