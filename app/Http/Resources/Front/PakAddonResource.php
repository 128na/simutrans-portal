<?php

namespace App\Http\Resources\Front;

use App\Models\PakAddonCount;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PakAddonResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection
            ->map(fn (PakAddonCount $p) => [
                'pak_slug' => $p->pak_slug,
                'addon_slug' => $p->addon_slug,
                'pak' => __("category.pak.{$p->pak_slug}"),
                'addon' => __("category.addon.{$p->addon_slug}"),
                'count' => $p->count,
            ])
            ->groupBy('pak');
    }
}
