<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User\LoginHistory;
use Illuminate\Database\Eloquent\Collection;

final class LoginHistoryRepository
{
    public function __construct(public LoginHistory $model) {}

    /**
     * @return Collection<int,LoginHistory>
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->query()
            ->where('user_id', $userId)->latest()
            ->limit(10)
            ->get();
    }
}
