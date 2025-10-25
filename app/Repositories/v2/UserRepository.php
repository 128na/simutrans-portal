<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Enums\ArticleStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository
{
    public function __construct(public User $model) {}

    /**
     * @return Collection<int,User>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['users.id', 'users.nickname', 'users.name'])
            ->whereExists(
                fn ($q) => $q->selectRaw(1)
                    ->from('articles as a')
                    ->whereColumn('a.user_id', 'users.id')
                    ->where('a.status', ArticleStatus::Publish)
            )
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     role: \App\Enums\UserRole::User,
     *     password: string,
     *     invited_by?: int,
     * } $data
     */
    public function store(array $data): User
    {
        return $this->model->create($data);
    }
}
