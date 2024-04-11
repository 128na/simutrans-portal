<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

final class GenerateSidebar extends BaseGenerator
{
    public function __construct(
        private readonly DeleteUnrelatedTags $deleteUnrelatedTags,
        private readonly CountPakAddon $countPakAddon,
        private readonly CountUserAddon $countUserAddon,
    ) {
    }

    #[\Override]
    protected function getJsonData(): array
    {
        ($this->deleteUnrelatedTags)();

        return [
            'userAddonCounts' => ($this->countUserAddon)(),
            'pakAddonCounts' => ($this->countPakAddon)(),
        ];
    }

    #[\Override]
    protected function getJsonName(): string
    {
        return 'sidebar.json';
    }
}
