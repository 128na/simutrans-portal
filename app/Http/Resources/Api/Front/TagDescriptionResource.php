<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class TagDescriptionResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'editable' => $this->resource->editable,
            'createdBy' => $this->resource->createdBy?->name,
            'createdAt' => $this->resource->created_at->toIso8601String(),
            'lastModifiedBy' => $this->resource->lastModifiedBy?->name,
            'lastModifiedAt' => $this->resource->last_modified_at
                ? $this->resource->last_modified_at->toIso8601String()
                : $this->resource->updated_at->toIso8601String(),
        ];
    }
}
