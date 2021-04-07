<?php

namespace App\Models;

use App\Models\User\BookmarkItem;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'role',
        'name',
        'email',
        'email_verified_at',
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
    | 初期化時設定
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->syncRelatedData();
        });
        self::updated(function ($model) {
            Cache::flush();
        });
    }

    private function syncRelatedData()
    {
        if (Profile::where('user_id', $this->id)->doesntExist()) {
            $this->profile()->create();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 通知
    |--------------------------------------------------------------------------
    */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

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
    public function myAttachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function bookmarkItemables(): MorphToMany
    {
        return $this->morphToMany(BookmarkItem::class, 'bookmark_itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopeAdmin($query)
    {
        return $query->where('role', config('role.admin'));
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */

    /**
     * ユーザーが管理者か.
     */
    public function isAdmin()
    {
        return $this->role === config('role.admin');
    }
}
