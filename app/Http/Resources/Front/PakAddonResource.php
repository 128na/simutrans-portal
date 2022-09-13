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
                'pak' => __("category.pak.{$p->pak_slug}"),
                'addon' => __("category.addon.{$p->addon_slug}"),
                'url' => route('category.pak.addon', [$p->pak_slug, $p->addon_slug]),
                'count' => $p->count,
            ])
            ->groupBy('pak');
    }
}
