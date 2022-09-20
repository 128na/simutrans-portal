<?php

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'avatar_url' => $this->profile->avatar_url,
            'description' => $this->profile->data->description,
            'twitter' => $this->profile->data->twitter,
            'website' => $this->profile->data->website,
        ];
    }
}
