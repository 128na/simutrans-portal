<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ユーザー毎の投稿数（メニュー表示用）.
 */
class UserAddonCount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'count',
    ];
}
