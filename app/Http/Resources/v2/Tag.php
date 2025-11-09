<?php

declare(strict_types=1);

namespace App\Http\Resources\v2;

use App\Models\Tag as ModelsTag;
use Illuminate\Http\Resources\Json\JsonResource;

final class Tag extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsTag);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'editable' => $this->resource->editable ?? true,
            'created_by' => $this->when($this->resource->createdBy, fn() => [
                'id' => $this->resource->createdBy->id,
                'name' => $this->resource->createdBy->name,
            ]),
            'last_modified_by' => $this->when($this->resource->lastModifiedBy, fn() => [
                'id' => $this->resource->lastModifiedBy->id,
                'name' => $this->resource->lastModifiedBy->name,
            ]),
            'last_modified_at' => $this->resource->last_modified_at?->format('Y-m-d\TH:i'),
            'articles_count' => $this->resource->articles_count,
        ];
    }
}
