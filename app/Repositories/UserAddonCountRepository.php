<?php

namespace App\Repositories;

use App\Models\UserAddonCount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserAddonCountRepository extends BaseRepository
{
    private const DELETE_SQL = 'DELETE FROM user_addon_counts';
    private const INSERT_SQL = "INSERT INTO user_addon_counts (user_id, user_name, count) (
        SELECT
            u.id user_id, u.name user_name, COUNT(a.id) count
        FROM
            users u
                LEFT JOIN
            articles a ON a.user_id = u.id
                AND a.status = 'publish'
                AND a.deleted_at IS NULL
        WHERE u.deleted_at IS NULL
        GROUP BY u.id
        HAVING COUNT(a.id) > 0
        ORDER BY COUNT(a.id) DESC)";

    /**
     * @var UserAddonCount
     */
    protected $model;

    public function __construct(UserAddonCount $model)
    {
        $this->model = $model;
    }

    /**
     * 再集計する.
     */
    public function recount()
    {
        DB::transaction(function () {
            DB::statement(self::DELETE_SQL);
            DB::statement(self::INSERT_SQL);
        });
    }

    public function get(): Collection
    {
        return $this->model->select('user_id', 'user_name', 'count')->get();
    }
}
