<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

final class TagDescriptionResource extends JsonResource
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
            'description' => $this->resource->description,
            'editable' => $this->resource->editable,
            'createdBy' => $this->resource->createdBy?->name,
            'createdAt' => $this->resource->created_at?->toIso8601String(),
            'lastModifiedBy' => $this->resource->lastModifiedBy?->name,
            'lastModifiedAt' => $this->resource->last_modified_at
                ? $this->resource->last_modified_at->toIso8601String()
                : $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
