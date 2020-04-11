<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Categories extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'name' => $item->name,
                'url' => route('category', [$item->type, $item->slug]),
                'api' => route('api.v2.articles.category', [$item->id]),
            ];
        });
    }
}
