<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Models\UserAddonCount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class RecountUserAddonCount
{
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

    /**
     * @return Collection<int,UserAddonCount>
     */
    public function __invoke(): Collection
    {
        UserAddonCount::truncate();
        DB::statement(self::INSERT_SQL);

        return UserAddonCount::select('user_id', 'user_name', 'user_nickname', 'count')->get();
    }
}
