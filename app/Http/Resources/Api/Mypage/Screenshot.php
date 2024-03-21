<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Article;
use App\Models\Screenshot as ModelsScreenshot;
use Illuminate\Http\Resources\Json\JsonResource;

class Screenshot extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsScreenshot);

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'links' => $this->resource->links,
            'status' => $this->resource->status,
            'attachments' => $this->resource->attachments->pluck('id'),
            'articles' => $this->resource->articles
                ->filter(fn (Article $article): bool => $article->is_publish)
                ->map(fn (Article $article): array => [
                    'id' => $article->id,
                    'title' => $article->title,
                ])
                ->values(),
            'extra' => $this->resource->extra,
        ];
    }
}
