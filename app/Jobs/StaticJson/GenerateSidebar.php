<?php

declare(strict_types=1);

namespace App\Jobs\StaticJson;

use App\Http\Resources\Api\Front\PakAddonResource;
use App\Http\Resources\Api\Front\UserAddonResource;
use App\Services\Front\SidebarService;

class GenerateSidebar extends BaseGenerator
{
    protected function getJsonData(): array
    {
        $service = app(SidebarService::class);

        return [
            'userAddonCounts' => new UserAddonResource($service->userAddonCounts()),
            'pakAddonCounts' => new PakAddonResource($service->pakAddonsCounts()),
        ];
    }

    protected function getJsonName(): string
    {
        return 'sidebar.json';
    }
}
