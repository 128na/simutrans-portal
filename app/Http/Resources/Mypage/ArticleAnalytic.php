<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleAnalytic extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof Article);

        return [
            'id' => $this->resource->id,
            'viewCounts' => $this->resource->viewCounts->pluck('count', 'period'),
            'conversionCounts' => $this->resource->conversionCounts->pluck('count', 'period'),
            'pastViewCount' => (int) $this->resource->past_view_count,
            'pastConversionCount' => (int) $this->resource->past_conversion_count,
        ];
    }
}
