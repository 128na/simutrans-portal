<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Screenshot as ModelsScreenshot;
use App\Models\User;
use Carbon\CarbonImmutable;
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
        assert($this->resource->user instanceof User);
        assert($this->resource->updated_at instanceof CarbonImmutable);

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'links' => $this->resource->links,
            'attachments' => $this->resource->attachments
                ->sortBy('order')
                ->values()
                ->map(fn (Attachment $attachment): array => [
                    'id' => $attachment->id,
                    'url' => $attachment->url,
                    'caption' => $attachment->caption,
                ]),
            'articles' => $this->resource->articles
                ->filter(fn (Article $article): bool => $article->is_publish)
                ->map(fn (Article $article): array => [
                    'id' => $article->id,
                    'title' => $article->title,
                ])
                ->values(),
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
            ],
            'updated_at' => $this->resource->updated_at->toDateTimeString(),
        ];
    }
}
