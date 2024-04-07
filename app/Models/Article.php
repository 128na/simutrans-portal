<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\ToArticleContents;
use App\Constants\DefaultThumbnail;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
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
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

/**
 * @mixin IdeHelperArticle
 */
class Article extends Model implements Feedable
{
    use HasFactory;
    use Notifiable;

    /**
     * @use Slugable<Article>
     */
    use Slugable;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
        'pr',
        'published_at',
        'modified_at',
    ];

    protected $casts = [
        'contents' => ToArticleContents::class,
        'status' => ArticleStatus::class,
        'post_type' => ArticlePostType::class,
        'published_at' => 'immutable_datetime',
        'modified_at' => 'immutable_datetime',
        'pr' => 'boolean',
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
        if (! $this->user->email) {
            throw new Exception('email not found');
        }

        return $this->user->email;
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
     * @return BelongsToMany<Category>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return BelongsTo<User,Article>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ViewCount>
     */
    public function viewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class);
    }

    /**
     * @return HasOne<ViewCount>
     */
    public function todaysViewCount(): HasOne
    {
        return $this->hasOne(ViewCount::class)
            ->where('type', ViewCount::TYPE_DAILY)
            ->where('period', now()->format('Ymd'));
    }

    /**
     * @return HasMany<ViewCount>
     */
    public function dailyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_DAILY);
    }

    /**
     * @return HasMany<ViewCount>
     */
    public function monthlyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_MONTHLY);
    }

    /**
     * @return HasMany<ViewCount>
     */
    public function yearlyViewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::TYPE_YEARLY);
    }

    /**
     * @return HasOne<ViewCount>
     */
    public function totalViewCount(): HasOne
    {
        return $this->hasOne(ViewCount::class)->where('type', ViewCount::TYPE_TOTAL);
    }

    /**
     * @return HasMany<ConversionCount>
     */
    public function conversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class);
    }

    /**
     * @return HasOne<ConversionCount>
     */
    public function todaysConversionCount(): HasOne
    {
        return $this->hasOne(ConversionCount::class)
            ->where('type', ConversionCount::TYPE_DAILY)
            ->where('period', now()->format('Ymd'));
    }

    /**
     * @return HasMany<ConversionCount>
     */
    public function dailyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_DAILY);
    }

    /**
     * @return HasMany<ConversionCount>
     */
    public function monthlyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_MONTHLY);
    }

    /**
     * @return HasMany<ConversionCount>
     */
    public function yearlyConversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::TYPE_YEARLY);
    }

    /**
     * @return HasOne<ConversionCount>
     */
    public function totalConversionCount(): HasOne
    {
        return $this->hasOne(ConversionCount::class)->where('type', ConversionCount::TYPE_TOTAL);
    }

    /**
     * @return HasOne<Ranking>
     */
    public function ranking(): HasOne
    {
        return $this->hasOne(Ranking::class);
    }

    /**
     * この記事から関連付けた記事
     *
     * @return MorphToMany<Article>
     */
    public function articles(): MorphToMany
    {
        return $this->morphToMany(Article::class, 'articlable');
    }

    /**
     * この記事が関連付けられた記事
     *
     * @return MorphToMany<Article>
     */
    public function relatedArticles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'articlable');
    }

    /**
     * この記事が関連付けられたスクリーンショット
     *
     * @return MorphToMany<Screenshot>
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
    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeWithUserTrashed(Builder $builder): void
    {
        $builder->withoutGlobalScope('WithoutTrashedUser');
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeUser(Builder $builder, User $user): void
    {
        $builder->where('user_id', $user->id);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeActive(Builder $builder): void
    {
        $builder->where('status', ArticleStatus::Publish);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeAddon(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::AddonPost, ArticlePostType::AddonIntroduction]);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeLinkCheckTarget(Builder $builder): void
    {
        $builder->where('post_type', ArticlePostType::AddonIntroduction)
            ->where(
                fn ($query) => $query->whereNull('contents->exclude_link_check')
                    ->orWhere('contents->exclude_link_check', false)
            );
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopePage(Builder $builder): void
    {
        $builder->where('post_type', [ArticlePostType::Page, ArticlePostType::Markdown]);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopePak(Builder $builder, string $slug): void
    {
        $builder->whereHas('categories', fn ($query) => $query->pak()->slug($slug));
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeAnnounce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereHas('categories', fn ($query) => $query->page()->slug('announce'));
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeWithoutAnnounce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereDoesntHave('categories', fn ($query) => $query->page()->slug('announce'));
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeRankingOrder(Builder $builder): void
    {
        $builder->join('rankings', 'rankings.article_id', '=', 'articles.id')
            ->orderBy('rankings.rank', 'asc');
    }

    /**
     * @param  Builder<Article>  $builder
     */
    public function scopeCategory(Builder $builder, Category $category): void
    {
        $builder->join('article_category', function (JoinClause $joinClause) use ($category): void {
            $joinClause->on('article_category.article_id', '=', 'articles.id')
                ->where('article_category.category_id', $category->id);
        });
    }

    /**
     * @param  Builder<Article>  $builder
     */
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

    /**
     * @param  Builder<Article>  $builder
     */
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
        return $this->getPublicDisk()->url($this->has_thumbnail && $this->thumbnail
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
        return $this->categories->filter(fn ($category): bool => $category->type === CategoryType::Pak);
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoryAddonsAttribute()
    {
        return $this->categories->filter(fn ($category): bool => $category->type === CategoryType::Addon);
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoryPak128PositionsAttribute()
    {
        return $this->categories->filter(fn ($category): bool => $category->type === CategoryType::Pak128Position);
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
        return $this->categories->some(fn ($category): bool => $category->type === CategoryType::Page && $category->slug === 'announce');
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

        return $this->getPublicDisk()->url($image instanceof \App\Models\Attachment
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
            'articleUserName' => $this->user->name,
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
            'link' => route('articles.show', ['userIdOrNickname' => $this->user->nickname ?? $this->user_id, 'articleSlug' => $this->slug]),
            'authorName' => $this->user->name ?? '',
        ]);
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
