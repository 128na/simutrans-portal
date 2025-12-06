<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Casts\ToProfileData;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperProfile
 */
final class Profile extends Model
{
    /** @var array<string, mixed> */
    protected $attributes = [
        'data' => '{}',
    ];

    protected $fillable = [
        'user_id',
        'data',
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

    public function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }

    /**
     * @return Attribute<Attachment|null, never>
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(get: function () {
            $id = (int) $this->data->avatar;
            if ($id !== 0) {
                return $this->attachments->first(fn (Attachment $attachment): bool => $id === $attachment->id);
            }

            return null;
        });
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
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'data' => ToProfileData::class,
        ];
    }
}
