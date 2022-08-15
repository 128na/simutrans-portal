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
                'name' => __("category.{$c->type}.{$c->slug}"),
                'type' => __("category.type.{$c->type}"),
                'url' => route('category', ['type' => $c->type, 'slug' => $c->slug]),
            ]),
            'tags' => $this->tags->map(fn (Tag $t) => [
                'name' => $t->name,
                'url' => route('tag', $t),
            ]),
            'user' => [
                'name' => $this->user->name,
                'url' => route('articles.show', $this->user),
            ],
            'thumbnail_url' => $this->thumbnail_url,
            'download_url' => $this->when($this->is_addon_post, route('articles.download', $this->slug)),
            'url' => route('articles.show', $this->slug),
            'published_at' => $this->published_at ? $this->published_at->toDateTimeString() : '未投稿',
            'modified_at' => $this->modified_at->toDateTimeString(),
            'file_info' => $this->when($this->hasFileInfo, $this->file->fileInfo->data),
        ];
    }
}
