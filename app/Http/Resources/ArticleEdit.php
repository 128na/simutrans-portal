<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article as ModelsArticle;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleEdit extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsArticle);

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'status' => $this->resource->status,
            'post_type' => $this->resource->post_type,
            'contents' => $this->resource->contents,
            'categories' => $this->resource->categories->pluck('id'),
            'tags' => $this->resource->tags->pluck('id'),
            'articles' => $this->resource->articles->pluck('id'),
            'attachments' => $this->resource->attachments->pluck('id'),
            'created_at' => $this->resource->created_at?->format('Y-m-d\TH:i'),
            'published_at' => $this->resource->published_at?->format('Y-m-d\TH:i'),
            'modified_at' => $this->resource->modified_at?->format('Y-m-d\TH:i'),
        ];
    }
}
