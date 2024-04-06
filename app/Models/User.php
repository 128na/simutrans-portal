<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\BulkZippableInterface;
use App\Enums\UserRole;
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
use Illuminate\Support\Facades\Config;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property UserRole $role 権限
 * @property string $name ユーザー名
 * @property string|null $nickname 表示名
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Carbon\CarbonImmutable|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property int|null $invited_by 紹介ユーザーID
 * @property string|null $invitation_code 紹介用コード
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \App\Models\BulkZip|null $bulkZippable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $createdTags
 * @property-read int|null $created_tags_count
 * @property-read User|null $invited
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $invites
 * @property-read int|null $invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $invitesReclusive
 * @property-read int|null $invites_reclusive_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $lastModifiedBy
 * @property-read int|null $last_modified_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoginHistory> $loginHistories
 * @property-read int|null $login_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $myAttachments
 * @property-read int|null $my_attachments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $screenshots
 * @property-read int|null $screenshots_count
 *
 * @method static Builder|User admin()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User query()
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
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
        'role' => UserRole::class,
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
        static::created(function ($model): void {
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
        return Config::string('logging.channels.slack.url');
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
    /**
     * @return HasMany<Article>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * @return HasOne<Profile>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * 自身が投稿した添付
     *
     * @return HasMany<Attachment>
     */
    public function myAttachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @return MorphOne<BulkZip>
     */
    public function bulkZippable(): MorphOne
    {
        return $this->morphOne(BulkZip::class, 'bulk_zippable');
    }

    /**
     * @return BelongsTo<User,User>
     */
    public function invited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * @return HasMany<User>
     */
    public function invites(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    /**
     * @return HasMany<User>
     */
    public function invitesReclusive(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by')->with(['invites']);
    }

    /**
     * @return HasMany<Tag>
     */
    public function createdTags(): HasMany
    {
        return $this->hasMany(Tag::class, 'created_by');
    }

    /**
     * @return HasMany<Tag>
     */
    public function lastModifiedBy(): HasMany
    {
        return $this->hasMany(Tag::class, 'lastModifiedBy');
    }

    /**
     * @return HasMany<LoginHistory>
     */
    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * @return HasMany<Screenshot>
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(Screenshot::class);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    /**
     * @param  Builder<User>  $builder
     */
    public function scopeAdmin(Builder $builder): void
    {
        $builder->where('role', UserRole::Admin);
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
        return $this->role === UserRole::Admin;
    }

    /**
     * @return array{userId:int,userName:string}
     */
    public function getInfoLogging(): array
    {
        return [
            'userId' => $this->id,
            'userName' => $this->name,
        ];
    }
}
