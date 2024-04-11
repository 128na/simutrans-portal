<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

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
            'invitation_url' => $this->resource->invitation_code ? route('invite.index', $this->resource->invitation_code) : null,
            'profile' => new Profile($this->resource->profile),
            'admin' => $this->resource->isAdmin(),
            'verified' => (bool) $this->resource->email_verified_at,
            'attachments' => Attachment::collection($this->resource->profile?->attachments),
            'two_factor' => (bool) $this->resource->two_factor_secret,
        ];
    }
}
