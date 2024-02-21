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
            ->map(fn (Attachment $a) => [
                'id' => $a->id,
                'url' => $this->when($a->is_image, $a->url),
                'fileInfo' => $this->when($a->fileInfo !== null, static fn () => $a->fileInfo?->data),
            ])->toArray();
    }
}
