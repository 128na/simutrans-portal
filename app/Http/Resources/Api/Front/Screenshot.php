<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

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
            'attachments' => new AttachmentResource($this->resource->attachments),
            'articles' => $this->resource->articles->map(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
            ]),
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
            ],
            'updated_at' => $this->resource->updated_at->toDateTimeString(),
        ];
    }
}
