<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

final class TagResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof Tag);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'count' => $this->resource->articles_count,
        ];
    }
}
