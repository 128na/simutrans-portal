<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article as ModelsArticle;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User\Profile;
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
            'url' => route('articles.show', ['userIdOrNickname' => $this->resource->user->nickname ?? $this->resource->id, 'articleSlug' => $this->resource->slug]),
            'thumbnail' => $this->resource->thumbnail->url,
            'description' => $this->resource->contents->getDescription(),
            'categories' => $this->resource->categories->map(fn(Category $category): array => [
                'id' => $category->id,
                'type' => $category->type,
                'slug' => $category->slug,
            ]),
            'tags' => $this->resource->tags->map(fn(Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]),
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
                'nickname' => $this->resource->user->nickname,
                'profile' => $this->when($this->resource->user->profile instanceof Profile, fn(): array => [
                    'data' => $this->resource->user->profile->data,
                    'attachments' => $this->resource->user->profile->attachments->map(fn($attachment): array => [
                        'id' => $attachment->id,
                        'thumbnail' => $attachment->thumbnail,
                        'original_name' => $attachment->original_name,
                        'url' => $attachment->url,
                    ]),
                ]),
            ],
            'published_at' => $this->resource->published_at?->format('Y/m/d H:i'),
            'modified_at' => $this->resource->modified_at?->format('Y/m/d H:i'),
        ];
    }
}
