<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class Articles extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        return $this->collection->map(fn($item): \App\Http\Resources\ArticleList => new Article($item))->all();
    }
}
