<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleAnalytic extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof Article);

        return [
            $this->resource->id,
            $this->resource->viewCounts->pluck('count', 'period'),
            $this->resource->conversionCounts->pluck('count', 'period'),
        ];
    }
}
