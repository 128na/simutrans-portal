<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleAnalytics extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         * id
         * conversion_counts
         * view_counts
         */
        return $this->collection->map(function ($item) {
            return [
                $item->id,
                $item->viewCounts->pluck('count', 'period'),
                $item->conversionCounts->pluck('count', 'period'),
            ];
        });
    }
}
