<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

final class AttachmentEdit extends JsonResource
{
    #[\Override]
    public function toArray(null|\Illuminate\Http\Request $request): array
    {
        assert($this->resource instanceof Attachment);

        return [
            'id' => $this->resource->id,
            'attachmentable_type' => class_basename($this->resource->attachmentable_type ?? ''),
            'attachmentable_id' => $this->resource->attachmentable_id,
            'type' => $this->resource->type,
            'original_name' => $this->resource->original_name,
            'thumbnail' => $this->resource->thumbnail,
            'url' => $this->resource->url,
            'size' => $this->resource->size,
            'fileInfo' => $this->when(
                $this->resource->attachmentable_type !== Profile::class && $this->resource->fileInfo,
                fn(): mixed => $this->resource->fileInfo?->data,
            ),
            'caption' => $this->when($this->resource->is_image, $this->resource->caption),
            'order' => $this->when($this->resource->is_image, $this->resource->order),
            'attachmentable' => $this->whenLoaded('attachmentable', function (): null|array {
                $attachmentable = $this->resource->attachmentable;

                return $attachmentable instanceof Article ? $attachmentable->only(['id', 'title']) : null;
            }),
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
