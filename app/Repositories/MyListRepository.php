<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MyList;
use App\Models\User;
use App\Repositories\Concerns\HasCrud;
use Illuminate\Contracts\Pagination\Paginator;

class MyListRepository
{
    use HasCrud;

    public function __construct(
        private readonly MyList $model,
    ) {}

    /**
     * ユーザーのマイリスト一覧取得（ページネーション付き）
     *
     * @return Paginator<int, MyList>
     */
    public function paginateForUser(User $user, int $page = 1, int $perPage = 20, string $sortField = 'updated_at', string $sortDirection = 'desc'): Paginator
    {
        $query = $this->model
            ->where('user_id', $user->id)
            ->withCount('items')
            ->orderBy($sortField, $sortDirection);

        /** @var Paginator<int, MyList> $result */
        $result = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return $result;
    }

    /**
     * 公開リストを slug で取得（存在しない場合は例外）
     */
    public function findOrFailPublicBySlug(string $slug): MyList
    {
        return $this->model
            ->where('is_public', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
