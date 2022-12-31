<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\UserAddonCount;
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
            ->map(fn (UserAddonCount $u) => [
                'user_id' => $u->user_id,
                'name' => $u->user_name,
                'count' => $u->count,
            ])->toArray();
    }
}
