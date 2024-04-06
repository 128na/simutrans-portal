<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ユーザー毎の投稿数（メニュー表示用）.
 *
 * @property int $id
 * @property int $user_id
 * @property string $user_name
 * @property string|null $user_nickname 表示名
 * @property int $count
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddonCount whereUserNickname($value)
 * @mixin \Eloquent
 */
class UserAddonCount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_nickname',
        'count',
    ];
}
