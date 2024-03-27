<?php

namespace App\Models;

use App\Enums\ScreenshotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Screenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'links',
        'status',
    ];

    protected $casts = [
        'links' => 'array',
        'status' => ScreenshotStatus::class,
    ];

    protected static function booted()
    {
        // 論理削除されていないユーザーを持つ
        static::addGlobalScope('WithoutTrashedUser', function (Builder $builder): void {
            $builder->has('user');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function articles(): MorphToMany
    {
        return $this->morphToMany(Article::class, 'articlable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    public function scopePublish(Builder $builder): void
    {
        $builder->where('status', ScreenshotStatus::Publish);
    }

    public function getIsPublishAttribute(): bool
    {
        return $this->status === ScreenshotStatus::Publish;
    }

    /**
     * @return array{screenshotId:int,screenshotTitle:string,screenshotStatus:ScreenshotStatus,screenshotUserName:string}
     */
    public function getInfoLogging(): array
    {
        $this->loadMissing('user');

        return [
            'screenshotId' => $this->id,
            'screenshotTitle' => $this->title,
            'screenshotStatus' => $this->status,
            'screenshotUserName' => $this->user?->name ?? '',
        ];
    }
}
