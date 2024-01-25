<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Article;
use App\Models\Category;
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
            'slug' => urldecode($this->resource->slug),
            'status' => $this->resource->status,
            'post_type' => $this->resource->post_type,
            'contents' => $this->resource->contents,
            'categories' => $this->resource->categories->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => __("category.{$c->type}.{$c->slug}"),
                'type' => $c->type,
                'slug' => $c->slug,
            ]),
            'tags' => $this->resource->tags->map(fn (Tag $t) => [
                'id' => $t->id,
                'name' => $t->name,
            ]),
            'user' => [
                'id' => $this->resource->user?->id,
                'nickname' => $this->resource->user?->nickname,
                'name' => $this->resource->user?->name,
            ],
            'published_at' => $this->resource->published_at?->toIso8601String() ?? '未投稿',
            'modified_at' => $this->resource->modified_at?->toIso8601String(),
            'file_info' => $this->when($this->resource->hasFileInfo, fn () => $this->resource->file?->fileInfo?->data),
            'attachments' => new AttachmentResource($this->resource->attachments),
            'download' => $this->when($this->resource->isAddonPost, fn () => route('articles.download', [
                'article' => $this->resource,
                'download' => 'download' . $this->ext(),
            ])),
        ];
    }

    private function ext(): string
    {
        $ext = $this->resource?->file?->extension;

        return $ext ? ".{$ext}" : '';
    }
}
