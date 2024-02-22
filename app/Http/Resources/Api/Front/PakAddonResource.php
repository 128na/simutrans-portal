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
            ->map(fn (PakAddonCount $pakAddonCount): array => [
                'pak_slug' => $pakAddonCount->pak_slug,
                'addon_slug' => $pakAddonCount->addon_slug,
                'pak' => __('category.pak.'.$pakAddonCount->pak_slug),
                'addon' => __('category.addon.'.$pakAddonCount->addon_slug),
                'count' => $pakAddonCount->count,
            ])
            ->groupBy('pak')
            ->toArray();
    }
}
