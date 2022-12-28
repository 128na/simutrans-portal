<?php

namespace App\Http\Resources\Api\Mypage;

use App\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Invites extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'created_at' => $user->created_at?->toIso8601String(),
        ])->toArray();
    }
}
