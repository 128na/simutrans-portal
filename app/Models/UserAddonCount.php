<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * ユーザー毎の投稿数（メニュー表示用）
 */
class UserAddonCount extends Model
{
    private const DELETE_SQL = 'DELETE FROM user_addon_counts';
    private const INSERT_SQL = "INSERT INTO user_addon_counts (user_id, user_name, count) (
        SELECT
            u.id user_id, u.name user_name, COUNT(a.id) count
        FROM
            users u
                LEFT JOIN
            articles a ON a.user_id = u.id
        WHERE
            role = 'user'
        GROUP BY u.id
        HAVING COUNT(a.id) > 0
        ORDER BY COUNT(a.id) DESC)";

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'count',
    ];

    public static function recount()
    {
        DB::transaction(function () {
            DB::statement(self::DELETE_SQL);
            DB::statement(self::INSERT_SQL);
        });
    }

}
