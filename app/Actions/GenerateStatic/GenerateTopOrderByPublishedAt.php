<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Enums\CategoryType;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PrArticleResource;
use App\Services\Front\ArticleService;

final class GenerateTopOrderByPublishedAt extends BaseGenerator
{
    public function __construct(private readonly ArticleService $articleService)
    {
    }

    protected function getJsonData(): array
    {
        $pr = $this->articleService->prArticle();

        return [
            'paks' => [
                'pak128japan' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '128-japan', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'pak128' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '128', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'pak64' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '64', true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'rankings' => ArticleResource::collection($this->articleService->paginateRanking(true)),
                'pages' => ArticleResource::collection($this->articleService->paginatePages(true, ArticleService::ORDER_BY_PUBLISHED_AT)),
                'announces' => ArticleResource::collection($this->articleService->paginateAnnouces(true, ArticleService::ORDER_BY_PUBLISHED_AT)),
            ],
            'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
        ];
    }

    protected function getJsonName(): string
    {
        return 'top.published_at.json';
    }
}
