<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
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


    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */
    public function getContents($key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }
    public function setContents($key, $value)
    {
        $tmp = $this->data;
        $tmp[$key] = $value;
        $this->data = $tmp;
    }
}
