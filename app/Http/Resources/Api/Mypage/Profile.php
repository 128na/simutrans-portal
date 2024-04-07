<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\User\Profile as UserProfile;
use Illuminate\Http\Resources\Json\JsonResource;

final class Profile extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof UserProfile);

        return [
            'id' => $this->resource->id,
            'data' => $this->resource->data,
        ];
    }
}
