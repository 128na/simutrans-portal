<?php

namespace App\Models;

use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
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

        self::deleting(function($model) {
            $model->deleteFileHandler();
        });
    }
    private function deleteFileHandler()
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
    | 一般
    |--------------------------------------------------------------------------
    */
    public static function createFromFile($file, $user_id)
    {
        return self::create([
            'user_id'       => $user_id,
            'path'          => $file->store('user/'.$user_id, 'public'),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function getIsImageAttribute()
    {
        $path = public_path('storage/'.$this->path);
        $mime = mime_content_type($path);
        return stripos($mime, 'image') !== false;
    }
    public function getThumbnailAttribute()
    {
        return $this->is_image
            ? asset('storage/'.$this->path)
            : asset('storage/'.config('attachment.thumbnail-file'));
    }
    public function getUrlAttribute()
    {
        return asset('storage/'.$this->path);
    }
}
