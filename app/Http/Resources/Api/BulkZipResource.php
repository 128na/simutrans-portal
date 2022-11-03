<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class BulkZipResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'generated' => (bool) $this->generated,
            'url' => $this->when($this->generated, Storage::disk('public')->url($this->path)),
            'generated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
