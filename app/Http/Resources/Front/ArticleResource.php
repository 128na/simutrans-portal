<?php

namespace App\Http\Resources\Front;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\Contents\Sections\SectionImage;
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
            'thumbnail_url' => $this->thumbnail_url,
            'url' => route('articles.show', $this->slug),
            'published_at' => $this->published_at ? $this->published_at->toDateTimeString() : '未投稿',
            'modified_at' => $this->modified_at->toDateTimeString(),
            'download_url' => $this->when($this->is_addon_post, fn () => route('articles.download', $this->slug)),
            'images' => $this->when($this->is_page, fn () => $this->getImages()),
            'file_info' => $this->when($this->hasFileInfo, fn () => $this->file->fileInfo->data),
        ];
    }

    private function getImages()
    {
        $ids = [];
        foreach ($this->contents->sections as $section) {
            if ($section instanceof SectionImage) {
                $ids[] = $section->id;
            }
        }

        return $this->attachments
            ->filter(fn (Attachment $a) => in_array($a->id, $ids))
            ->map(fn (Attachment $a) => ['id' => $a->id, 'url' => $a->url])
            ->values();
    }
}
