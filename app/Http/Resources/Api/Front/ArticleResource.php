<?php

namespace App\Http\Resources\Api\Front;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => urldecode($this->slug),
            'status' => $this->status,
            'post_type' => $this->post_type,
            'contents' => $this->contents,
            'categories' => $this->categories->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => __("category.{$c->type}.{$c->slug}"),
                'type' => $c->type,
                'slug' => $c->slug,
            ]),
            'tags' => $this->tags->map(fn (Tag $t) => [
                'id' => $t->id,
                'name' => $t->name,
            ]),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'published_at' => $this->published_at?->toIso8601String() ?? '未投稿',
            'modified_at' => $this->modified_at->toIso8601String(),
            'file_info' => $this->when($this->hasFileInfo, fn () => $this->file->fileInfo->data),
            'attachments' => new AttachmentResource($this->attachments),
        ];
    }
}
