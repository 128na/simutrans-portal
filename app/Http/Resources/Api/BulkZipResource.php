<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class BulkZipResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->resource->uuid,
            'generated' => (bool) $this->resource->generated,
            'url' => $this->when($this->resource->generated, Storage::disk('public')->url($this->resource->path)),
            'generated_at' => $this->resource->updated_at->toIso8601String(),
        ];
    }
}
