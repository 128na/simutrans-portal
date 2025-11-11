<?php

declare(strict_types=1);

namespace App\Http\Resources\v2;

use App\Models\User as ModelsUser;
use Illuminate\Http\Resources\Json\JsonResource;

final class User extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof ModelsUser);

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'nickname' => $this->resource->nickname,
            'email' => $this->resource->email,
            'profile' => [
                'id' => $this->resource->profile->id,
                'data' => $this->resource->profile->data,
            ],
            'role' => $this->resource->role,
        ];
    }
}
