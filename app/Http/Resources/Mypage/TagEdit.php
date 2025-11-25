<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

final class TagEdit extends JsonResource
{
    /** @var Tag */
    public $resource;

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'editable' => $this->resource->editable ?? true,
            'created_by' => $this->when((bool) $this->resource->createdBy, function (): array {
                /** @var \App\Models\User $user */
                $user = $this->resource->createdBy;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
            'last_modified_by' => $this->when((bool) $this->resource->lastModifiedBy, function (): array {
                /** @var \App\Models\User $user */
                $user = $this->resource->lastModifiedBy;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
            'last_modified_at' => $this->resource->last_modified_at?->format('Y-m-d\TH:i'),
            'articles_count' => $this->resource->articles_count,
        ];
    }
}
