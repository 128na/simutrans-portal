<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Attachment\FileInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attachmentable_id',
        'attachmentable_type',
        'original_name',
        'path',
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

        self::deleting(static function ($model) : void {
            $model->deleteFileHandler();
        });
    }

    public function deleteFileHandler(): ?bool
    {
        return Storage::disk('public')->delete($this->path);
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

    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

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
        return Storage::disk('public')->exists($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    public function getTypeAttribute(): string
    {
        $mime = Storage::disk('public')->mimeType($this->path) ?: '';

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
        $mime = Storage::disk('public')->mimeType($this->path) ?: '';

        return stripos((string) $mime, 'image/png') !== false;
    }

    public function getThumbnailAttribute(): string
    {
        return match ($this->type) {
            'image' => Storage::disk('public')->url($this->path),
            'zip' => Storage::disk('public')->url(config('attachment.thumbnail-zip')),
            'movie' => Storage::disk('public')->url(config('attachment.thumbnail-movie')),
            default => Storage::disk('public')->url(config('attachment.thumbnail-file')),
        };
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk('public')->path($this->path);
    }

    public function getFileContentsAttribute(): ?string
    {
        return Storage::disk('public')->get($this->path);
    }

    public function getExtensionAttribute(): string
    {
        $tmp = explode('.', (string) $this->original_name);
        if (count($tmp) > 1) {
            return array_pop($tmp);
        }

        return '';
    }
}
