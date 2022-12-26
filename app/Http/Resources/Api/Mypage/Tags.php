<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Tags extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection->map(fn ($item) => new Tag($item))->toArray();
    }
}
