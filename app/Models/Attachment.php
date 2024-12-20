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
 * @mixin IdeHelperAttachment
 */
final class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachmentable_id',
        'attachmentable_type',
        'original_name',
        'path',
        'caption',
        'order',
    ];

    protected $hidden = [
        'path',
    ];

    public function deleteFileHandler(): ?bool
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

    private function pathExists(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => $this->getPublicDisk()->exists($this->path));
    }

    private function isImage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn(): bool => $this->type === 'image');
    }

    private function type(): \Illuminate\Database\Eloquent\Casts\Attribute
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

    private function isPng(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function (): bool {
            $mime = $this->getPublicDisk()->mimeType($this->path) ?: '';
            return mb_stripos((string) $mime, 'image/png') !== false;
        });
    }

    private function thumbnail(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => match ($this->type) {
            'image' => $this->getPublicDisk()->url($this->path),
            'zip' => $this->getPublicDisk()->url(DefaultThumbnail::ZIP),
            'movie' => $this->getPublicDisk()->url(DefaultThumbnail::MOVIE),
            default => $this->getPublicDisk()->url(DefaultThumbnail::FILE),
        });
    }

    private function url(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => $this->getPublicDisk()->url($this->path));
    }

    private function fullPath(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => $this->getPublicDisk()->path($this->path));
    }

    private function fileContents(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => $this->getPublicDisk()->get($this->path));
    }

    private function extension(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function (): string {
            $tmp = explode('.', (string) $this->original_name);
            if (count($tmp) > 1) {
                return array_pop($tmp);
            }
            return '';
        });
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

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
