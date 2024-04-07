<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class Invite extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
