<?php

namespace App\Models;

use App\Contracts\Models\BulkZippableInterface;
use App\Models\User\Profile;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, BulkZippableInterface
{
    use Notifiable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'role',
        'name',
        'invited_by',
        'invitation_code',
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
    }

    public function routeNotificationForSlack($notification)
    {
        return config('logging.channels.slack.url');
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

    public function bulkZippable(): MorphOne
    {
        return $this->morphOne(BulkZip::class, 'bulk_zippable');
    }

    public function invited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    public function invitesReclusive(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by')->with(['invites']);
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
