<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

class PrArticleResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Article);

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => urldecode((string) $this->resource->slug),
            'user' => [
                'id' => $this->resource->user?->id,
                'nickname' => $this->resource->user?->nickname,
            ],
        ];
    }
}
