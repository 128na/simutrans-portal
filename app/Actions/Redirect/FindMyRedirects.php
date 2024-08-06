<?php

declare(strict_types=1);

namespace App\Actions\Redirect;

use App\Models\Redirect;
use App\Models\User;
use App\Repositories\RedirectRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class FindMyRedirects
{
    public function __construct(
        private RedirectRepository $redirectRepository,
    ) {}

    /**
     * @return Collection<int,Redirect>
     */
    public function __invoke(User $user): Collection
    {
        return $this->redirectRepository->findByUser($user->id);
    }
}
