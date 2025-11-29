<?php

declare(strict_types=1);

namespace App\Http\Resources\Frontend;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserShow extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'nickname' => $this->resource->nickname,
        ];
    }
}
