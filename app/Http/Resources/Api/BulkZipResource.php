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
            'url' => $this->generated ? Storage::disk('public')->url($this->path) : null,
        ];
    }
}
