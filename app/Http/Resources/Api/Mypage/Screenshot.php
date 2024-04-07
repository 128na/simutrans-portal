<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Screenshot as ModelsScreenshot;
use Illuminate\Http\Resources\Json\JsonResource;

final class Screenshot extends JsonResource
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
            'attachments' => $this->resource->attachments
                ->map(fn (Attachment $attachment): array => [
                    'id' => $attachment->id,
                    'caption' => $attachment->caption,
                    'order' => $attachment->order,
                ])
                ->sortBy('order')
                ->values(),
            'articles' => $this->resource->articles
                ->filter(fn (Article $article): bool => $article->is_publish)
                ->map(fn (Article $article): array => [
                    'id' => $article->id,
                    'title' => $article->title,
                ])
                ->values(),
        ];
    }
}
