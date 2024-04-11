<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScreenshotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperScreenshot
 */
final class Screenshot extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'links',
        'status',
        'published_at',
    ];

    protected $casts = [
        'links' => 'array',
        'status' => ScreenshotStatus::class,
        'published_at' => 'immutable_datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    /**
     * @return MorphMany<Attachment>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    /**
     * @return BelongsTo<User,Screenshot>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphToMany<Article>
     */
    public function articles(): MorphToMany
    {
        return $this->morphToMany(Article::class, 'articlable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    /**
     * @param  Builder<Screenshot>  $builder
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
            'screenshotUserName' => $this->user->name,
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        // 論理削除されていないユーザーを持つ
        self::addGlobalScope('WithoutTrashedUser', function (Builder $builder): void {
            $builder->has('user');
        });
    }
}
