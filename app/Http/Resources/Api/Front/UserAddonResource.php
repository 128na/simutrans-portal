<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\UserAddonCount;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddonResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof UserAddonCount);

        return [
            'user_id' => $this->resource->user_id,
            'name' => $this->resource->user_name,
            'nickname' => $this->resource->user_nickname,
        ];
    }
}
