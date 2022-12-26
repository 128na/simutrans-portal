<?php

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->resource->name,
            'avatar_url' => $this->resource->profile->avatar_url,
            'description' => $this->resource->profile->data->description,
            'twitter' => $this->resource->profile->data->twitter,
            'website' => $this->resource->profile->data->website,
        ];
    }
}
