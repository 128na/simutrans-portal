<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

final class TagEdit extends JsonResource
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
            'editable' => $this->resource->editable ?? true,
            'created_by' => $this->when((bool) $this->resource->createdBy, fn (): array => [
                'id' => $this->resource->createdBy->id,
                'name' => $this->resource->createdBy->name,
            ]),
            'last_modified_by' => $this->when((bool) $this->resource->lastModifiedBy, fn (): array => [
                'id' => $this->resource->lastModifiedBy->id,
                'name' => $this->resource->lastModifiedBy->name,
            ]),
            'last_modified_at' => $this->resource->last_modified_at?->format('Y-m-d\TH:i'),
            'articles_count' => $this->resource->articles_count,
        ];
    }
}
