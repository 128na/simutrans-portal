<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pak-アドオン毎の投稿数（メニュー表示用）.
 *
 * @mixin IdeHelperPakAddonCount
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
