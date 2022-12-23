<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'invitation_url' => $this->invitation_code ? route('invite.index', $this->invitation_code) : null,
            'profile' => new Profile($this->profile),
            'admin' => $this->isAdmin(),
            'verified' => (bool) $this->email_verified_at,
            'attachments' => new Attachments($this->profile->attachments),
        ];
    }
}
