<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Enums\CategoryType;
use App\Http\Resources\Api\Front\ArticleResource;
use App\Http\Resources\Api\Front\PrArticleResource;
use App\Services\Front\ArticleService;

final class GenerateTopOrderByModifiedAt extends BaseGenerator
{
    public function __construct(private readonly ArticleService $articleService) {}

    #[\Override]
    protected function getJsonData(): array
    {
        $pr = $this->articleService->prArticle();

        return [
            'paks' => [
                'pak128japan' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '128-japan', true)),
                'pak128' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '128', true)),
                'pak64' => ArticleResource::collection($this->articleService->paginateByCategory(CategoryType::Pak, '64', true)),
                'rankings' => ArticleResource::collection($this->articleService->paginateRanking(true)),
                'pages' => ArticleResource::collection($this->articleService->paginatePages(true)),
                'announces' => ArticleResource::collection($this->articleService->paginateAnnouces(true)),
            ],
            'pr' => $pr instanceof \App\Models\Article ? new PrArticleResource($pr) : null,
        ];
    }

    #[\Override]
    protected function getJsonName(): string
    {
        return 'top.modified_at.json';
    }
}
