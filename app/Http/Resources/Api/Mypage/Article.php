<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'post_type' => $this->post_type,
            'contents' => $this->contents,
            'categories' => $this->categories->pluck('id'),
            'tags' => $this->tags->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'url' => route('articles.show', $this->slug),
            'views' => $this->totalViewCount->count ?? 0,
            'conversions' => $this->totalConversionCount->count ?? 0,
        ];
    }
}
