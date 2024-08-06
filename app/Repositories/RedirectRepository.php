<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Redirect>
 */
final class RedirectRepository extends BaseRepository
{
    public function __construct(Redirect $redirect)
    {
        parent::__construct($redirect);
    }

    public function findOrFailByPath(string $path): Redirect
    {
        return $this->model->from($path)->firstOrFail();
    }

    /**
     * @return Collection<int,Redirect>
     */
    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
