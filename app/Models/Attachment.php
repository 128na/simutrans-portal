<?php

namespace App\Models;

use App\Models\User;
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
}
