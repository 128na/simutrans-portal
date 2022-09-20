<?php

namespace App\Http\Resources\Api\Front;

use App\Models\UserAddonCount;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAddonResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection
            ->map(fn (UserAddonCount $u) => [
                'user_id' => $u->user_id,
                'name' => $u->user_name,
                'count' => $u->count,
            ]);
    }
}
