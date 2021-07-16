<?php

namespace App\Models\User;

use App\Casts\ToProfileData;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): MorphMany
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
        return Storage::disk('public')->url($this->has_avatar
            ? $this->avatar->path
            : config('attachment.no-avatar')
        );
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
