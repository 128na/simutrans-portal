<?php

namespace App\Http\Resources\Front;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'post_type' => $this->post_type,
            'contents' => $this->contents,
            'categories' => $this->categories->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => __("category.{$c->type}.{$c->slug}"),
                'url' => route('category', ['type' => $c->type, 'slug' => $c->slug]),
            ]),
            'tags' => $this->tags->map(fn (Tag $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'url' => route('tag', $t),
            ]),
            'user' => [
                'name' => $this->user->name,
                'url' => route('articles.show', $this->user),
            ],
            'url' => route('articles.show', $this->slug),
            'published_at' => $this->published_at ? $this->published_at->toDateTimeString() : '未投稿',
            'modified_at' => $this->modified_at->toDateTimeString(),
            'file_info' => $this->when($this->hasFileInfo, fn () => $this->file->fileInfo->data),
        ];
    }
}
