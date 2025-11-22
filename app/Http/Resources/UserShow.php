<?php

declare(strict_types=1);

namespace App\Http\Resources;

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

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'nickname' => $this->resource->nickname,
            'role' => $this->resource->role,
            'profile' => [
                'id' => $this->resource->profile->id,
                'data' => $this->resource->profile->data,
                'attachments' => $this->resource->profile->attachments->map(fn ($attachment): array => [
                    'id' => $attachment->id,
                    'thumbnail' => $attachment->thumbnail,
                    'original_name' => $attachment->original_name,
                    'url' => $attachment->url,
                ]),
            ],
        ];
    }
}
