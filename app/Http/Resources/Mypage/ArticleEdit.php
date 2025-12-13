<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Models\Article as ModelsArticle;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleEdit extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsArticle);

        /** @var \Carbon\CarbonImmutable|null $createdAt */
        $createdAt = $this->resource->created_at;
        /** @var \Carbon\CarbonImmutable|null $publishedAt */
        $publishedAt = $this->resource->published_at;
        /** @var \Carbon\CarbonImmutable|null $modifiedAt */
        $modifiedAt = $this->resource->modified_at;

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
            'created_at' => $createdAt?->format('Y-m-d\TH:i'),
            'published_at' => $publishedAt?->format('Y-m-d\TH:i'),
            'modified_at' => $modifiedAt?->format('Y-m-d\TH:i'),
        ];
    }
}
