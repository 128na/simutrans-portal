<?php

declare(strict_types=1);

namespace App\Http\Resources\Mypage;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserShow extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        $profile = $this->resource->profile;

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'nickname' => $this->resource->nickname,
            'role' => $this->resource->role,
            'profile' => [
                'id' => $profile?->id,
                'data' => $profile?->data,
                'attachments' => $profile?->attachments->map(fn ($attachment): array => [
                    'id' => $attachment->id,
                    'thumbnail' => $attachment->thumbnail,
                    'original_name' => $attachment->original_name,
                    'url' => $attachment->url,
                ]) ?? [],
            ],
        ];
    }
}
