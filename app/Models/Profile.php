<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\User;
use App\Traits\JsonFieldable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Profile extends Model
{
    use JsonFieldable;

    protected $attributes = [
        'data' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'data',
    ];
    protected $casts = [
        'data' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        self::updated(function($model) {
            Redis::flushAll();
        });
    }


    public function getJsonableField()
    {
        return 'data';
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
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function getAvatarAttribute()
    {
        $id = $this->getContents('avatar');
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }
    public function getHasAvatarAttribute()
    {
        return !!$this->avatar;
    }
    public function getAvatarUrlAttribute()
    {
        return $this->has_avatar
             ? asset('storage/'.$this->avatar->path)
             : asset('storage/'.config('attachment.no-avatar'));
    }
    public function getHasFileAttribute()
    {
        return !is_null($this->file);
    }
}
