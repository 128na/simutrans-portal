<?php

namespace App\Http\Resources\Front;

use App\Models\UserAddonCount;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAddonResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection
            ->map(fn (UserAddonCount $u) => [
                'name' => $u->user_name,
                'url' => route('user', [$u->user_id]),
                'count' => $u->count,
            ]);
    }
}
