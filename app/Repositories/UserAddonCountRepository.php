<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAddonCount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @extends BaseRepository<UserAddonCount>
 */
class UserAddonCountRepository extends BaseRepository
{
    private const DELETE_SQL = 'DELETE FROM user_addon_counts';

    private const INSERT_SQL = "INSERT INTO user_addon_counts (user_id, user_name, user_nickname, count) (
        SELECT
            u.id user_id, u.name user_name, u.nickname user_nickname, COUNT(a.id) count
        FROM
            users u
                LEFT JOIN
            articles a ON a.user_id = u.id
                AND a.status = 'publish'
                AND a.deleted_at IS NULL
        WHERE u.deleted_at IS NULL
        GROUP BY u.id, u.name, u.nickname
        HAVING COUNT(a.id) > 0
        ORDER BY COUNT(a.id) DESC)";

    public function __construct(UserAddonCount $userAddonCount)
    {
        parent::__construct($userAddonCount);
    }

    /**
     * 再集計する.
     */
    public function recount(): void
    {
        DB::transaction(function (): void {
            DB::statement(self::DELETE_SQL);
            DB::statement(self::INSERT_SQL);
        });
    }

    /**
     * @return Collection<int,UserAddonCount>
     */
    public function get(): Collection
    {
        return $this->model->select('user_id', 'user_name', 'user_nickname', 'count')->get();
    }
}
