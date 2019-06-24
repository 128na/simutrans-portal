<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Profile;
use App\Models\Article;

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
        return $this->collection->map(function($item) {
            $attachmentable = [];
            if ($item->attachmentable_type === Profile::class) {
                $attachmentable['type'] = __('message.profile');
            }
            if ($item->attachmentable_type === Article::class) {
                $attachmentable['type'] = __('category.post.'.$item->attachmentable->post_type);
                $attachmentable['id'] = $item->attachmentable_id;
                $attachmentable['title'] = $item->attachmentable->title;
            }

            return [
                'id' => $item->id,
                'is_image' => $item->is_image,
                'original_name' => $item->original_name,
                'thumbnail' => $item->thumbnail,
                'url' => $item->url,
                'attachmentable' => $attachmentable,
            ];
        });
    }
}
