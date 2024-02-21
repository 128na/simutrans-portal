<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Front;

use App\Models\PakAddonCount;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PakAddonResource extends ResourceCollection
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return $this->collection
            ->map(static fn (PakAddonCount $p): array => [
                'pak_slug' => $p->pak_slug,
                'addon_slug' => $p->addon_slug,
                'pak' => __('category.pak.'.$p->pak_slug),
                'addon' => __('category.addon.'.$p->addon_slug),
                'count' => $p->count,
            ])
            ->groupBy('pak')
            ->toArray();
    }
}
