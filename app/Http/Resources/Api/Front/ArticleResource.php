<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Article;
use App\Models\Category;
use App\Models\Screenshot;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'user' => [
                'id' => $this->resource->user?->id,
                'nickname' => $this->resource->user?->nickname,
                'name' => $this->resource->user?->name,
            ],
            'articles' => $this->resource->articles
                ->filter(fn (Article $article): bool => $article->is_publish)
                ->map(fn (Article $article): array => [
                    'id' => $article->id,
                    'title' => $article->title,
                ])
                ->values(),
            'relatedArticles' => $this->resource->relatedArticles
                ->filter(fn (Article $article): bool => $article->is_publish)
                ->map(fn (Article $article): array => [
                    'id' => $article->id,
                    'title' => $article->title,
                ])
                ->values(),
            'relatedScreenshots' => $this->resource->relatedScreenshots
                ->filter(fn (Screenshot $screenshot): bool => $screenshot->is_publish)
                ->map(fn (Screenshot $screenshot): array => [
                    'id' => $screenshot->id,
                    'title' => $screenshot->title,
                ])
                ->values(),
            'published_at' => $this->resource->published_at?->toIso8601String() ?? '未投稿',
            'modified_at' => $this->resource->modified_at?->toIso8601String(),
            'file_info' => $this->when($this->resource->hasFileInfo, fn () => $this->resource->file?->fileInfo?->data),
            'attachments' => new AttachmentResource($this->resource->attachments),
        ];
    }
}
