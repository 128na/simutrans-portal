<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleAnalytic extends JsonResource
{
    #[\Override]
    public function toArray(null|\Illuminate\Http\Request $request): array
    {
        assert($this->resource instanceof Article);

        return [
            $this->resource->id,
            $this->resource->viewCounts->pluck('count', 'period'),
            $this->resource->conversionCounts->pluck('count', 'period'),
        ];
    }
}
