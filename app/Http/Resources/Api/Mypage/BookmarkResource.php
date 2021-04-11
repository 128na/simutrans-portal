<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'is_public' => (bool) $this->is_public,
            'bookmarkItems' => BookmarkItemResource::collection($this->bookmarkItems),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
