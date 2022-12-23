<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Attachments extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'attachmentable_type' => class_basename($item->attachmentable_type),
                'attachmentable_id' => $item->attachmentable_id,
                'type' => $item->type,
                'original_name' => $item->original_name,
                'thumbnail' => $item->thumbnail,
                'url' => $item->url,
                'fileInfo' => $this->when($item->fileInfo, fn () => $item->fileInfo->data),
            ];
        });
    }
}
