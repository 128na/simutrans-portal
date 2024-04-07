<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Repositories\PakAddonCountRepository;
use App\Repositories\UserAddonCountRepository;
use Illuminate\Database\Eloquent\Collection;

class SidebarService
{
    public function __construct(
        private readonly PakAddonCountRepository $pakAddonCountRepository,
        private readonly UserAddonCountRepository $userAddonCountRepository
    ) {
    }

    /**
     * @return Collection<int, \App\Models\UserAddonCount>
     */
    public function userAddonCounts(): Collection
    {
        return $this->userAddonCountRepository->get();
    }

    /**
     * @return Collection<int, \App\Models\PakAddonCount>
     */
    public function pakAddonsCounts(): Collection
    {
        return $this->pakAddonCountRepository->get();
    }
}
