<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'post_type' => $this->post_type,
            'contents' => $this->contents->getDescription(),
            'url' => route('articles.show', $this->slug),
            'author' => $this->contents->author,
            'categories' => new Categories($this->categories),
            'tags' => new Tags($this->tags),
            'created_by' => new User($this->user),
            'created_at' => $this->created_at->format('Y-m-dTH-i-s'),
            'updated_at' => $this->updated_at->format('Y-m-dTH-i-s'),
        ];
    }
}
