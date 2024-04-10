<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use Illuminate\Support\Facades\DB;

final class CountUserAddon
{
    private const SELECT_SQL = "SELECT
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
        ORDER BY COUNT(a.id) DESC";

    /**
     * @return array<int,array{user_id:int,user_name:string,user_nickname:?string,count:int}>
     */
    public function __invoke(): array
    {
        /**
         * @var array<int,array{user_id:int,user_name:string,user_nickname:?string,count:int}>
         */
        $items = json_decode(json_encode(DB::select(self::SELECT_SQL)) ?: '', true);

        return $items;
    }
}
