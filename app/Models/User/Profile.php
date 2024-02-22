<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Casts\ToProfileData;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

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
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        self::updated(function ($model): void {
            Cache::flush();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
            return $this->attachments->first(static fn (Attachment $attachment): bool => $id === $attachment->id);
        }

        return null;
    }

    public function getHasAvatarAttribute(): bool
    {
        return (bool) $this->avatar;
    }

    public function getAvatarUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->has_avatar && $this->avatar
            ? $this->avatar->path
            : config('attachment.no-avatar'));
    }
}
