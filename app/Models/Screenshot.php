<?php

namespace App\Models;

use App\Enums\ScreenshotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title タイトル
 * @property string $description 説明
 * @property array $links リンク先一覧
 * @property ScreenshotStatus $status 公開ステータス
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read bool $is_publish
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ScreenshotFactory factory($count = null, $state = [])
 * @method static Builder|Screenshot newModelQuery()
 * @method static Builder|Screenshot newQuery()
 * @method static Builder|Screenshot publish()
 * @method static Builder|Screenshot query()
 * @mixin \Eloquent
 */
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
            'screenshotUserName' => $this->user?->name ?? '',
        ];
    }
}
