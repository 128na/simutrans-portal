<?php

namespace App\Http\Resources\Api\Mypage;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\User\Bookmark;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'bookmark_itemable_type' => $this->bookmark_itemable_type,
            'bookmark_itemable_id' => $this->bookmark_itemable_id,
            'memo' => $this->memo,
            'order' => $this->order,
            'title' => $this->handleTitle($this->bookmarkItemable),
        ];
    }

    private function handleTitle($bookmarkItemable)
    {
        switch (get_class($bookmarkItemable)) {
            case Article::class:
                return $bookmarkItemable->title;
            case Bookmark::class:
                return $bookmarkItemable->title;
            case Category::class:
                return __("category.{$bookmarkItemable->type}.{$bookmarkItemable->slug}");
            case Tag::class:
                return $bookmarkItemable->name;
            case User::class:
                return $bookmarkItemable->name;
        }
        throw new Exception('unknown bookmarkItemable provided:', [$bookmarkItemable]);
    }
}
