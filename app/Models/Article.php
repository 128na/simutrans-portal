<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\ToArticleContents;
use App\Constants\DefaultThumbnail;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article\ConversionCount;
use App\Models\Article\Ranking;
use App\Models\Article\ViewCount;
use App\Models\Contents\AddonPostContent;
use App\Traits\Slugable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Model implements Feedable
{
    use HasFactory;
    use Notifiable;
    use Slugable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
        'published_at',
        'modified_at',
    ];

    protected $casts = [
        'contents' => ToArticleContents::class,
        'status' => ArticleStatus::class,
        'post_type' => ArticlePostType::class,
        'published_at' => 'immutable_datetime',
        'modified_at' => 'immutable_datetime',
    ];

    protected static function booted()
    {
        // 論理削除されていないユーザーを持つ
        static::addGlobalScope('WithoutTrashedUser', function (Builder $builder): void {
            $builder->has('user');
        });
    }

    public function routeNotificationForMail(mixed $notification): string
    {
        if (! $this->user?->email) {
            throw new Exception('email not found');
        }

        return $this->user->email;
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function viewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class);
    }

    public function todaysViewCount(): HasOne
    {
        return $this->hasOne(ViewCount::class)
            ->where('type', ViewCount::TYPE_DAILY)
            ->where('period', now()->format('Ymd'));
    }

    public function dailyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_DAILY);
    }

    public function monthlyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_MONTHLY);
    }

    public function yearlyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_YEARLY);
    }

    public function totalViewCount(): HasOne
    {
        return $this->hasOne(ViewCount::class)->where('type', ViewCount::TYPE_TOTAL);
    }

    public function conversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class);
    }

    public function todaysConversionCount(): HasOne
    {
        return $this->hasOne(ConversionCount::class)
            ->where('type', ConversionCount::TYPE_DAILY)
            ->where('period', now()->format('Ymd'));
    }

    public function dailyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_DAILY);
    }

    public function monthlyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_MONTHLY);
    }

    public function yearlyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_YEARLY);
    }

    public function totalConversionCount(): HasOne
    {
        return $this->hasOne(ConversionCount::class)->where('type', ConversionCount::TYPE_TOTAL);
    }

    public function ranking(): HasOne
    {
        return $this->hasOne(Ranking::class);
    }

    /**
     * この記事から関連付けた記事
     */
    public function articles(): MorphToMany
    {
        return $this->morphToMany(Article::class, 'articlable');
    }

    /**
     * この記事が関連付けられた記事
     */
    public function relatedArticles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'articlable');
    }

    /**
     * この記事が関連付けられたスクリーンショット
     */
    public function relatedScreenshots(): MorphToMany
    {
        return $this->morphedByMany(Screenshot::class, 'articlable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    public function scopeWithUserTrashed(Builder $builder): void
    {
        $builder->withoutGlobalScope('WithoutTrashedUser');
    }

    public function scopeUser(Builder $builder, User $user): void
    {
        $builder->where('user_id', $user->id);
    }

    public function scopeActive(Builder $builder): void
    {
        $builder->where('status', ArticleStatus::Publish);
    }

    public function scopeAddon(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::AddonPost, ArticlePostType::AddonIntroduction]);
    }

    public function scopeLinkCheckTarget(Builder $builder): void
    {
        $builder->where('post_type', ArticlePostType::AddonIntroduction)
            ->where(
                fn ($query) => $query->whereNull('contents->exclude_link_check')
                    ->orWhere('contents->exclude_link_check', false)
            );
    }

    public function scopePage(Builder $builder): void
    {
        $builder->where('post_type', [ArticlePostType::Page, ArticlePostType::Markdown]);
    }

    public function scopePak(Builder $builder, string $slug): void
    {
        $builder->whereHas('categories', fn ($query) => $query->pak()->slug($slug));
    }

    public function scopeAnnounce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereHas('categories', fn ($query) => $query->page()->slug('announce'));
    }

    public function scopeWithoutAnnounce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereDoesntHave('categories', fn ($query) => $query->page()->slug('announce'));
    }

    public function scopeRankingOrder(Builder $builder): void
    {
        $builder->join('rankings', 'rankings.article_id', '=', 'articles.id')
            ->orderBy('rankings.rank', 'asc');
    }

    public function scopeCategory(Builder $builder, Category $category): void
    {
        $builder->join('article_category', function (JoinClause $joinClause) use ($category): void {
            $joinClause->on('article_category.article_id', '=', 'articles.id')
                ->where('article_category.category_id', $category->id);
        });
    }

    public function scopePakAddonCategory(Builder $builder, Category $pak, Category $addon): void
    {
        $builder->join('article_category', function (JoinClause $joinClause) use ($pak): void {
            $joinClause->on('article_category.article_id', '=', 'articles.id')
                ->where('article_category.category_id', $pak->id);
        });
        $builder->join('article_category', function (JoinClause $joinClause) use ($addon): void {
            $joinClause->on('article_category.article_id', '=', 'articles.id')
                ->where('article_category.category_id', $addon->id);
        });
    }

    public function scopeTag(Builder $builder, Tag $tag): void
    {
        $builder->join('article_tag', function (JoinClause $joinClause) use ($tag): void {
            $joinClause->on('article_tag.article_id', '=', 'articles.id')
                ->where('article_tag.tag_id', $tag->id);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
     */
    public function getIsAddonPostAttribute(): bool
    {
        return $this->post_type === ArticlePostType::AddonPost;
    }

    public function getIsPageAttribute(): bool
    {
        return $this->post_type === ArticlePostType::Page;
    }

    public function getIsPublishAttribute(): bool
    {
        return $this->status === ArticleStatus::Publish;
    }

    public function getIsReservationAttribute(): bool
    {
        return $this->status === ArticleStatus::Reservation;
    }

    public function getIsInactiveAttribute(): bool
    {
        return in_array($this->status, [
            ArticleStatus::Draft,
            ArticleStatus::Private,
            ArticleStatus::Trash,
        ]);
    }

    public function getHasThumbnailAttribute(): bool
    {
        return ! is_null($this->contents->thumbnail) && $this->thumbnail;
    }

    public function getThumbnailAttribute(): ?Attachment
    {
        $id = $this->contents->thumbnail;

        return $this->attachments->first(fn ($attachment): bool => (string) $id == $attachment->id);
    }

    public function getThumbnailUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->has_thumbnail && $this->thumbnail
            ? $this->thumbnail->path
            : DefaultThumbnail::NO_THUMBNAIL);
    }

    public function getHasFileAttribute(): bool
    {
        return $this->is_addon_post
            && $this->contents instanceof AddonPostContent
            && ! is_null($this->contents->file) && $this->file;
    }

    public function getFileAttribute(): ?Attachment
    {
        if ($this->contents instanceof AddonPostContent) {
            $id = $this->contents->file;

            return $this->attachments->first(fn ($attachment): bool => (string) $id == $attachment->id);
        }

        throw new Exception('invalid post type');
    }

    public function getHasFileInfoAttribute(): bool
    {
        return $this->hasFile && $this->file && $this->file->fileInfo;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoryPaksAttribute(): Collection
    {
        return $this->categories->filter(fn ($category): bool => $category->type === config('category.type.pak'));
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoryAddonsAttribute()
    {
        return $this->categories->filter(fn ($category): bool => $category->type === config('category.type.addon'));
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoryPak128PositionsAttribute()
    {
        return $this->categories->filter(fn ($category): bool => $category->type === config('category.type.pak128_position'));
    }

    public function getTodaysConversionRateAttribute(): string
    {
        if (! is_null($this->todaysConversionCount) && $this->todaysViewCount) {
            $rate = $this->todaysConversionCount->count / $this->todaysViewCount->count * 100;

            return sprintf('%.1f %%', $rate);
        }

        return 'N/A';
    }

    public function getMetaDescriptionAttribute(): string
    {
        return mb_strimwidth((string) $this->contents->getDescription(), 0, 300, '…');
    }

    public function getHeadlineDescriptionAttribute(): string
    {
        return mb_strimwidth((string) $this->contents->getDescription(), 0, 55, '…');
    }

    public function getUrlDecodedSlugAttribute(): string
    {
        return urldecode((string) $this->slug);
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
     */
    public function isAnnounce(): bool
    {
        return $this->categories->some(fn ($category): bool => $category->type === 'page' && $category->slug === 'announce');
    }

    public function hasCategory(string|int $id): bool
    {
        return $this->categories->some(fn ($category): bool => $category->id === (int) $id);
    }

    public function getImage(string|int $id): ?Attachment
    {
        return $this->attachments->first(
            fn ($attachment): bool => (int) $id == $attachment->id
        );
    }

    public function getImageUrl(int|string $id): string
    {
        $image = $this->getImage($id);

        return Storage::disk('public')->url($image instanceof \App\Models\Attachment
            ? $image->path
            : DefaultThumbnail::NO_THUMBNAIL);
    }

    /**
     * @return array{articleId:int,articleTitle:string,articleStatus:ArticleStatus,articleUserName:string}
     */
    public function getInfoLogging(): array
    {
        $this->loadMissing('user');

        return [
            'articleId' => $this->id,
            'articleTitle' => $this->title,
            'articleStatus' => $this->status,
            'articleUserName' => $this->user?->name ?? '',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RSS
    |--------------------------------------------------------------------------
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->contents->getDescription(),
            'updated' => $this->modified_at?->toMutable(), // CarbonImmutableは未対応
            'link' => route('articles.show', ['userIdOrNickname' => $this->user?->nickname ?? $this->user_id, 'articleSlug' => $this->slug]),
            'authorName' => $this->user->name ?? '',
        ]);
    }
}
