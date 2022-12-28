<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'invitation_url' => $this->resource->invitation_code ? route('invite.index', $this->resource->invitation_code) : null,
            'profile' => new Profile($this->resource->profile),
            'admin' => $this->resource->isAdmin(),
            'verified' => (bool) $this->resource->email_verified_at,
            'attachments' => new Attachments($this->resource->profile->attachments),
        ];
    }
}
