<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class Tag extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
        ];
    }
}
