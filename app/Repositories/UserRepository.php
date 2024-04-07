<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<User>
 */
final class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * 論理削除されているものも含めた一覧.
     *
     * @return Collection<int, User>
     */
    public function findAllWithTrashed(): Collection
    {
        return $this->model
            ->withTrashed()
            ->withCount(['articles' => fn ($q) => $q->withUserTrashed()->withTrashed()])
            ->get();
    }

    /**
     * 論理削除されているものも含めて探す.
     */
    public function findOrFailWithTrashed(int $id): User
    {
        return $this->model
            ->withTrashed()
            ->findOrFail($id);
    }

    /**
     * 論理削除状態を切り替える.
     */
    public function toggleDelete(User $user): void
    {
        $user->trashed()
            ? $user->restore()
            : $user->delete();
    }

    public function findByEmailWithTrashed(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->withTrashed()
            ->first();
    }

    /**
     * @return Collection<int, User>
     */
    public function findInvites(User $user): Collection
    {
        return $user->invites()->get();
    }
}
