<?php

declare(strict_types=1);

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
        return $this->collection->map(static fn ($item) => new Tag($item))->toArray();
    }
}
