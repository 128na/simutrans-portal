<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pak-アドオン毎の投稿数（メニュー表示用）.
 *
 * @property int $id
 * @property string $pak_slug
 * @property string $addon_slug
 * @property int $count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PakAddonCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PakAddonCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PakAddonCount query()
 *
 * @mixin \Eloquent
 */
class PakAddonCount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'pak_slug',
        'addon_slug',
        'count',
    ];
}
