<?php

namespace App\Models;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Profile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    // 自身が投稿した添付
    public function ownedAttachments()
    {
        return $this->hasMany(Attachment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */
    /**
     * ユーザーが管理者か
     */
    public function isAdmin()
    {
        return $this->role === config('role.admin');
    }
}
