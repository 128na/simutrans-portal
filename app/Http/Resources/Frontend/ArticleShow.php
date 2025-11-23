<?php

declare(strict_types=1);

namespace App\Http\Resources\Frontend;

use App\Models\Article as ModelsArticle;
use App\Models\Attachment\FileInfo;
use App\Models\User\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

final class ArticleShow extends JsonResource
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
            'post_type' => $this->resource->post_type,
            'contents' => $this->resource->contents,
            'user' => [
                'id' => $this->resource->user->id,
                'name' => $this->resource->user->name,
                'nickname' => $this->resource->user->nickname,
                'profile' => $this->when($this->resource->user->profile instanceof Profile, fn (): array => [
                    'data' => $this->resource->user->profile->data,
                    'attachments' => $this->resource->user->profile->attachments->map(fn ($attachment): array => [
                        'id' => $attachment->id,
                        'thumbnail' => $attachment->thumbnail,
                        'original_name' => $attachment->original_name,
                        'url' => $attachment->url,
                    ]),
                ]),
            ],
            'categories' => $this->resource->categories->map(fn ($category): array => [
                'id' => $category->id,
                'type' => $category->type->value,
                'slug' => $category->slug,
            ]),
            'tags' => $this->resource->tags->map(fn ($tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]),
            'articles' => $this->resource->articles->map(fn ($article): array => [
                'id' => $article->id,
                'title' => $article->title,
            ]),
            'relatedArticles' => $this->resource->relatedArticles->map(fn ($article): array => [
                'id' => $article->id,
                'title' => $article->title,
            ]),
            'attachments' => $this->resource->attachments->map(fn ($attachment): array => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'thumbnail' => $attachment->thumbnail,
                'url' => $attachment->url,
                'fileInfo' => $this->when($attachment->fileInfo instanceof FileInfo, fn (): array => [
                    'data' => [
                        'dates' => $attachment->fileInfo->getDats(),
                        'tabs' => $attachment->fileInfo->getTabs(),
                    ],
                ]),
            ]),
            'published_at' => $this->resource->published_at?->format('Y/m/d H:i'),
            'modified_at' => $this->resource->modified_at?->format('Y/m/d H:i'),
        ];
    }
}
