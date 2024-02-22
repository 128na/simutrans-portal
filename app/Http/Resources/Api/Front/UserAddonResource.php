<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAddonResource extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection
            ->map(static fn ($u): array => [
                'user_id' => $u->user_id,
                'name' => $u->user_name,
                'nickname' => $u->user_nickname,
                'count' => $u->count,
            ])->toArray();
    }
}
