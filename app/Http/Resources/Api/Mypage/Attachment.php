<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Attachment as ModelsAttachment;
use App\Models\User\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

final class Attachment extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsAttachment);

        return [
            'id' => $this->resource->id,
            'attachmentable_type' => class_basename($this->resource->attachmentable_type ?? ''),
            'attachmentable_id' => $this->resource->attachmentable_id,
            'type' => $this->resource->type,
            'original_name' => $this->resource->original_name,
            'thumbnail' => $this->resource->thumbnail,
            'url' => $this->resource->url,
            'fileInfo' => $this->when(
                $this->resource->attachmentable_type !== Profile::class && $this->resource->fileInfo,
                fn () => $this->resource->fileInfo?->data
            ),
            'caption' => $this->when($this->resource->is_image, $this->resource->caption),
            'order' => $this->when($this->resource->is_image, $this->resource->order),
        ];
    }
}
