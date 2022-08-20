<?php

namespace App\Http\Resources\Front;

use App\Models\Attachment;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection
            ->filter(fn (Attachment $a) => $a->is_image)
            ->map(fn (Attachment $a) => [
                'id' => $a->id,
                'url' => $a->url,
                'fileInfo' => $this->when($a->fileInfo, fn () => $a->fileInfo->data),
            ]);
    }
}
