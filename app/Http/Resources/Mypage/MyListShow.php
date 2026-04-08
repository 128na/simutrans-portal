<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Models\MyList as ModelsMyList;
use Illuminate\Http\Resources\Json\JsonResource;

class MyListShow extends JsonResource
{
    /** @var ModelsMyList */
    public $resource;

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'note' => $this->resource->note,
            'is_public' => $this->resource->is_public,
            'slug' => $this->resource->slug,
            'items_count' => $this->resource->items_count ?? 0,
            'created_at' => $this->resource->created_at?->format('Y/m/d H:i'),
            'updated_at' => $this->resource->updated_at?->format('Y/m/d H:i'),
        ];
    }
}
