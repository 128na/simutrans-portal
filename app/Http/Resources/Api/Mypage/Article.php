<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Article as ModelsArticle;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
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
            'categories' => $this->resource->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => __(sprintf('category.%s.%s', $category->type, $category->slug)),
                'type' => $category->type,
                'slug' => $category->slug,
            ]),
            'tags' => $this->resource->tags->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'published_at' => $this->resource->published_at?->toIso8601String(),
            'modified_at' => $this->resource->modified_at?->toIso8601String(),
            'url' => route('articles.show', ['userIdOrNickname' => $this->resource->user?->nickname ?? $this->resource->user_id, 'articleSlug' => $this->resource->slug]),
            'metrics' => [
                'totalViewCount' => $this->resource->totalViewCount->count ?? 0,
                'totalConversionCount' => $this->resource->totalConversionCount->count ?? 0,
            ],
        ];
    }
}
