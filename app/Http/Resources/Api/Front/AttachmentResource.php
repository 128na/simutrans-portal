<?php

namespace App\Http\Resources\Api\Front;

use App\Models\Attachment;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection
            ->map(fn (Attachment $a) => [
                'id' => $a->id,
                'url' => $this->when($a->is_image, $a->url),
                'fileInfo' => $this->when($a->fileInfo, fn () => $a->fileInfo->data),
            ]);
    }
}