<?php

namespace App\Models;

use App\Casts\ToProfileData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Profile extends Model
{
    use HasFactory;

    protected $attributes = [
        'data' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'data',
    ];
    protected $casts = [
        'data' => ToProfileData::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            Cache::flush();
        });
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
        $id = $this->data->avatar;

        return $this->attachments->first(fn ($attachment) => $id === $attachment->id);
    }

    public function getHasAvatarAttribute()
    {
        return (bool) $this->avatar;
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

    public function getHasTwitterAttribute()
    {
        return (bool) $this->data->twitter;
    }
}
