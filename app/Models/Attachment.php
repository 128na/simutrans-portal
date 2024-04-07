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
class Attachment extends Model
{
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

    /*
    |--------------------------------------------------------------------------
    | イベントハンドラ
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        self::deleting(function ($model): void {
            $model->deleteFileHandler();
        });
    }

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
     * @return BelongsTo<User,Attachment>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model,Attachment>
     */
    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasOne<FileInfo>
     */
    public function fileInfo(): HasOne
    {
        return $this->hasOne(FileInfo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
     */
    public function getPathExistsAttribute(): bool
    {
        return $this->getPublicDisk()->exists($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    public function getTypeAttribute(): string
    {
        $mime = $this->getPublicDisk()->mimeType($this->path) ?: '';

        if (stripos((string) $mime, 'image') !== false) {
            return 'image';
        }

        if (stripos((string) $mime, 'video') !== false) {
            return 'video';
        }

        if (stripos((string) $mime, 'text') !== false) {
            return 'text';
        }

        return 'file';
    }

    public function getIsPngAttribute(): bool
    {
        $mime = $this->getPublicDisk()->mimeType($this->path) ?: '';

        return stripos((string) $mime, 'image/png') !== false;
    }

    public function getThumbnailAttribute(): string
    {
        return match ($this->type) {
            'image' => $this->getPublicDisk()->url($this->path),
            'zip' => $this->getPublicDisk()->url(DefaultThumbnail::ZIP),
            'movie' => $this->getPublicDisk()->url(DefaultThumbnail::MOVIE),
            default => $this->getPublicDisk()->url(DefaultThumbnail::FILE),
        };
    }

    public function getUrlAttribute(): string
    {
        return $this->getPublicDisk()->url($this->path);
    }

    public function getFullPathAttribute(): string
    {
        return $this->getPublicDisk()->path($this->path);
    }

    public function getFileContentsAttribute(): ?string
    {
        return $this->getPublicDisk()->get($this->path);
    }

    public function getExtensionAttribute(): string
    {
        $tmp = explode('.', (string) $this->original_name);
        if (count($tmp) > 1) {
            return array_pop($tmp);
        }

        return '';
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
