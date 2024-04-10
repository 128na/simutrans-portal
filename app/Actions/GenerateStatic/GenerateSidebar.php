<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Http\Resources\Api\Front\PakAddonResource;
use App\Http\Resources\Api\Front\UserAddonResource;

final class GenerateSidebar extends BaseGenerator
{
    public function __construct(
        private readonly DeleteUnrelatedTags $deleteUnrelatedTags,
        private readonly RecountPakAddonCount $recountPakAddonCount,
        private readonly RecountUserAddonCount $recountUserAddonCount,
    ) {
    }

    protected function getJsonData(): array
    {
        ($this->deleteUnrelatedTags)();
        $pakAddonCount = ($this->recountPakAddonCount)();
        $userAddonCount = ($this->recountUserAddonCount)();

        return [
            'userAddonCounts' => UserAddonResource::collection($userAddonCount),
            'pakAddonCounts' => new PakAddonResource($pakAddonCount),
        ];
    }

    protected function getJsonName(): string
    {
        return 'sidebar.json';
    }
}
