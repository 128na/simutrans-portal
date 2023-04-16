<?php

declare(strict_types=1);

namespace App\Jobs\StaticJson;

use App\Http\Resources\Api\Front\ArticleResource;
use App\Services\Front\ArticleService;

class GenerateTopOrderByModifiedAt extends BaseGenerator
{
    protected function getJsonData(): array
    {
        /** @var ArticleService */
        $service = app(ArticleService::class);

        return [
            'pak128japan' => ArticleResource::collection($service->paginateByCategory('pak', '128-japan', true)),
            'pak128' => ArticleResource::collection($service->paginateByCategory('pak', '128', true)),
            'pak64' => ArticleResource::collection($service->paginateByCategory('pak', '64', true)),
            'rankings' => ArticleResource::collection($service->paginateRanking(true)),
            'pages' => ArticleResource::collection($service->paginatePages(true)),
            'announces' => ArticleResource::collection($service->paginateAnnouces(true)),
        ];
    }

    protected function getJsonName(): string
    {
        return 'top.modified_at.json';
    }
}
