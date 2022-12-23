<?php

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'count' => $this->resource->articles_count,
        ];
    }
}
