<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserProfileResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        return [
            'name' => $this->resource->name,
            'avatar_url' => $this->resource->profile?->avatar_url,
            'description' => $this->resource->profile?->data->description,
            'website' => $this->resource->profile?->data->website,
        ];
    }
}
