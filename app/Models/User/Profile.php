<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Casts\ToProfileData;
use App\Constants\DefaultThumbnail;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperProfile
 */
final class Profile extends Model
{
    /** @var array<string> */
    protected $attributes = [
        'data' => '{}',
    ];

    protected $fillable = [
        'user_id',
        'data',
    ];

    protected $casts = [
        'data' => ToProfileData::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphMany<Attachment,$this>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
     */
    public function getAvatarAttribute(): ?Attachment
    {
        $id = (int) $this->data->avatar;

        if ($id !== 0) {
            return $this->attachments->first(fn (Attachment $attachment): bool => $id === $attachment->id);
        }

        return null;
    }

    public function getHasAvatarAttribute(): bool
    {
        return (bool) $this->avatar;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->getPublicDisk()->url($this->has_avatar && $this->avatar
            ? $this->avatar->path
            : DefaultThumbnail::NO_AVATAR);
    }

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    #[\Override]
    protected static function boot(): void
    {
        parent::boot();

        self::updated(function ($model): void {
            Cache::flush();
        });
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
