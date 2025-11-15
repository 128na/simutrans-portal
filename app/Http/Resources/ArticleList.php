<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article as ModelsArticle;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleList extends JsonResource
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
            'categories' => $this->resource->categories->map(fn(Category $category): array => [
                'id' => $category->id,
                'name' => __(sprintf('category.%s.%s', $category->type->value, $category->slug)),
                'type' => $category->type,
                'slug' => $category->slug,
            ]),
            'tags' => $this->resource->tags->map(fn(Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]),
            'articles' => $this->resource
                ->articles
                ->filter(fn(ModelsArticle $modelsArticle): bool => $modelsArticle->is_publish)
                ->map(fn(ModelsArticle $modelsArticle): array => [
                    'id' => $modelsArticle->id,
                    'title' => $modelsArticle->title,
                ])
                ->values(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'published_at' => $this->resource->published_at?->toIso8601String(),
            'modified_at' => $this->resource->modified_at?->toIso8601String(),
            'url' => route('articles.show', [
                'userIdOrNickname' => $this->resource->user->nickname ?? $this->resource->user_id,
                'articleSlug' => $this->resource->slug,
            ]),
            'metrics' => [
                'totalViewCount' => $this->resource->totalViewCount->count ?? 0,
                'totalConversionCount' => $this->resource->totalConversionCount->count ?? 0,
            ],
        ];
    }
}
