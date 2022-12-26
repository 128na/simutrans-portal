<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleAnalytics extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        /*
         * id
         * conversion_counts
         * view_counts
         */
        return $this->collection->map(
            fn ($item) => [
                $item->id,
                $item->viewCounts->pluck('count', 'period'),
                $item->conversionCounts->pluck('count', 'period'),
            ]
        )->toArray();
    }
}
