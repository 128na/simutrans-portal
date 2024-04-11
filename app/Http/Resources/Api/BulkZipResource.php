<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\BulkZip;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

final class BulkZipResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof BulkZip);
        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $disk = Storage::disk('public');

        return [
            'uuid' => $this->resource->uuid,
            'generated' => $this->resource->generated,
            'url' => $this->when($this->resource->generated, $disk->url($this->resource->path ?? '')),
            'generated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
