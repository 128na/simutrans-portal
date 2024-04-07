<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Tag as ModelsTag;
use Illuminate\Http\Resources\Json\JsonResource;

final class Tag extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsTag);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
        ];
    }
}
