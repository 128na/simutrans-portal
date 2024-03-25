<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\Attachment;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentResource extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection
            ->map(fn (Attachment $attachment): array => [
                'id' => $attachment->id,
                'url' => $this->when($attachment->is_image, $attachment->url),
                'fileInfo' => $this->when($attachment->fileInfo !== null, fn () => $attachment->fileInfo?->data),
                'caption' => $this->when($attachment->is_image, $attachment->caption),
            ])
            ->toArray();
    }
}
