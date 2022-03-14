<?php

namespace App\Models;

use App\Contracts\Models\BulkZippableInterface;
use App\Models\Firebase\Project;
use App\Models\Firebase\ProjectUser;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use App\Models\User\Profile;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, BulkZippableInterface
{
    use Notifiable;
    use HasFactory;
    use SoftDeletes;
    // use HasApiTokens;

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
    const TITLE_NG_WORDS = ['#', '@', ':', '//'];

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
    }

    private function syncRelatedData()
    {
        if ($this->profile()->doesntExist()) {
            $this->profile()->create();
        }
        if ($this->bookmarks()->count() === 0) {
            $this->bookmarks()->create(['title' => 'ブックマーク']);
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
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // 自身が投稿した添付
    public function myAttachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkItemables(): MorphMany
    {
        return $this->morphMany(BookmarkItem::class, 'bookmark_itemable');
    }

    public function bulkZippable(): MorphOne
    {
        return $this->morphOne(BulkZip::class, 'bulk_zippable');
    }

    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
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
