<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Articles extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection->map(fn ($item): \App\Http\Resources\Api\Mypage\Article => new Article($item))->toArray();
    }
}
