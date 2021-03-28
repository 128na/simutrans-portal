<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Attachments extends ResourceCollection
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
                'id' => $item->id,
                'is_image' => $item->is_image,
                'original_name' => $item->original_name,
                'thumbnail' => $item->thumbnail,
                'url' => $item->url,
            ]
        );
    }
}
