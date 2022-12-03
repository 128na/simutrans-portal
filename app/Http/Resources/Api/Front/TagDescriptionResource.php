<?php

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class TagDescriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'editable' => $this->editable,
            'createdBy' => $this->createdBy?->name,
            'createdAt' => $this->created_at->toIso8601String(),
            'lastModifiedBy' => $this->lastModifiedBy?->name,
            'lastModifiedAt' => $this->last_modified_at
                ? $this->last_modified_at->toIso8601String()
                : $this->updated_at->toIso8601String(),
        ];
    }
}
