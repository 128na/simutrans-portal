<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Categories extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(
            fn ($item) => [
                'type' => $item->type,
                'slug' => $item->slug,
                'url' => route('category', [$item->type, $item->slug]),
                'api' => route('api.v2.articles.category', [$item->id]),
            ]
        );
    }
}
