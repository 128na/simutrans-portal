<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\BulkZippableInterface;
use App\Models\User\LoginHistory;
use App\Models\User\Profile;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements BulkZippableInterface, MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'role',
        'name',
        'nickname',
        'invited_by',
        'invitation_code',
        'email',
        'email_verified_at',
        'password',
        'two_factor_confirmed_at',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(static function ($model): void {
            $model->syncRelatedData();
        });
    }

    public function syncRelatedData(): void
    {
        if ($this->profile()->doesntExist()) {
            $this->profile()->create();
        }
    }

    public function routeNotificationForSlack(mixed $notification): string
    {
        return config('logging.channels.slack.url');
    }

    /*
    |--------------------------------------------------------------------------
    | 通知
    |--------------------------------------------------------------------------
    */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendEmailVerificationNotification(): void
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

    public function createdTags(): HasMany
    {
        return $this->hasMany(Tag::class, 'created_by');
    }

    public function lastModifiedBy(): HasMany
    {
        return $this->hasMany(Tag::class, 'lastModifiedBy');
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopeAdmin(Builder $query): void
    {
        $query->where('role', config('role.admin'));
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */

    /**
     * ユーザーが管理者か.
     */
    public function isAdmin(): bool
    {
        return $this->role === config('role.admin');
    }
}
