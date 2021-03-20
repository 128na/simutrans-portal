<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachmentable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
     */
    public function getPathExistsAttribute()
    {
        return Storage::disk('public')->exists($this->path);
    }

    public function getIsImageAttribute()
    {
        return $this->type === 'image';
    }

    public function getTypeAttribute()
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

    public function getIsPngAttribute()
    {
        $mime = Storage::disk('public')->mimeType($this->path);

        return stripos($mime, 'image/png') !== false;
    }

    public function getThumbnailAttribute()
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

    public function getUrlAttribute()
    {
        return asset('storage/'.$this->path);
    }

    public function getFullPathAttribute()
    {
        return Storage::disk('public')->path($this->path);
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
     */
    public static function createFromFile(UploadedFile $file, int $user_id)
    {
        return self::create([
            'user_id' => $user_id,
            'path' => $file->store('user/'.$user_id, 'public'),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
