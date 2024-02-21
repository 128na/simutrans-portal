<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\User\Profile;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Attachments extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection->map(fn ($item) => [
            'id' => $item->id,
            'attachmentable_type' => class_basename($item->attachmentable_type),
            'attachmentable_id' => $item->attachmentable_id,
            'type' => $item->type,
            'original_name' => $item->original_name,
            'thumbnail' => $item->thumbnail,
            'url' => $item->url,
            'fileInfo' => $this->when(
                $item->attachmentable_type !== Profile::class && $item->fileInfo,
                fn () => $item->fileInfo->data
            ),
        ])->toArray();
    }
}
