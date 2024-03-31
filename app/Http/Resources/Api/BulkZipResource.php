<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BulkZipResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $disk = Storage::disk('public');

        return [
            'uuid' => $this->resource->uuid,
            'generated' => (bool) $this->resource->generated,
            'url' => $this->when($this->resource->generated, $disk->url($this->resource->path)),
            'generated_at' => $this->resource->updated_at->toIso8601String(),
        ];
    }
}
