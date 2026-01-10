<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\DefaultThumbnail;
use App\Models\Attachment\FileInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $attachmentable_id 添付先ID
 * @property string|null $attachmentable_type 添付先クラス名
 * @property string $original_name オリジナルファイル名
 * @property string $path 保存先パス
 * @property string|null $thumbnail_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $caption キャプション（画像向け）
 * @property int $order 表示順（画像向け）
 * @property int $size ファイルサイズ(byte)
 * @property-read \Illuminate\Database\Eloquent\Model|null $attachmentable
 * @property-read FileInfo|null $fileInfo
 * @property-read mixed $full_path
 * @property-read bool $is_image
 * @property-read mixed $original
 * @property-read mixed $thumbnail
 * @property-read string $type
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\AttachmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperAttachment
 */
class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachmentable_id',
        'attachmentable_type',
        'original_name',
        'path',
        'thumbnail_path',
        'caption',
        'order',
        'size',
    ];

    protected $hidden = [
        'path',
    ];

    public function deleteFileHandler(): bool
    {
        return $this->getPublicDisk()->delete($this->path);
    }

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
     * @return MorphTo<Model,$this>
     */
    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasOne<FileInfo,$this>
     */
    public function fileInfo(): HasOne
    {
        return $this->hasOne(FileInfo::class);
    }

    public function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isImage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): bool => $this->type === 'image');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function type(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function (): string {
            $mime = $this->getPublicDisk()->mimeType($this->path) ?: '';
            if (mb_stripos((string) $mime, 'image') !== false) {
                return 'image';
            }

            if (mb_stripos((string) $mime, 'video') !== false) {
                return 'video';
            }

            if (mb_stripos((string) $mime, 'text') !== false) {
                return 'text';
            }

            return 'file';
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function thumbnail(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => match ($this->type) {
            'image' => $this->thumbnail_path
                ? $this->getPublicDisk()->url($this->thumbnail_path)
                : $this->getPublicDisk()->url($this->path),
            'zip' => $this->getPublicDisk()->url(DefaultThumbnail::ZIP),
            'movie' => $this->getPublicDisk()->url(DefaultThumbnail::MOVIE),
            default => $this->getPublicDisk()->url(DefaultThumbnail::FILE),
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function original(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->getPublicDisk()->url($this->path));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function fullPath(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->getPublicDisk()->path($this->path));
    }

    /*
    |--------------------------------------------------------------------------
    | イベントハンドラ
    |--------------------------------------------------------------------------
     */
    #[\Override]
    protected static function boot(): void
    {
        parent::boot();

        self::deleting(function ($model): void {
            $model->deleteFileHandler();
        });
    }
}
