<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

        self::deleting(function ($model) {
            $model->deleteFileHandler();
        });
    }

    public function deleteFileHandler()
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
        $mime = Storage::disk('public')->mimeType($this->path);

        if (stripos($mime, 'image') !== false) {
            return 'image';
        }
        if (stripos($mime, 'video') !== false) {
            return 'video';
        }
        if (stripos($mime, 'text') !== false) {
            return 'text';
        }

        return 'file';
    }

    public function getIsPngAttribute(): bool
    {
        $mime = Storage::disk('public')->mimeType($this->path);

        return stripos($mime, 'image/png') !== false;
    }

    public function getThumbnailAttribute(): string
    {
        switch ($this->type) {
            case 'image':
                return asset('storage/'.$this->path);
            case 'zip':
                return asset('storage/'.config('attachment.thumbnail-zip'));
            case 'movie':
                return asset('storage/'.config('attachment.thumbnail-movie'));
            case 'file':
            default:
                return asset('storage/'.config('attachment.thumbnail-file'));
        }
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk('public')->path($this->path);
    }
}
