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
            'profile' => new Profile($this->profile),
            'admin' => $this->isAdmin(),
            'verified' => !!$this->email_verified_at,
            'attachments' => new Attachments($this->profile->attachments),
        ];
    }
}
