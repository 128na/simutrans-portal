<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\ToArticleContents;
use App\Constants\DefaultThumbnail;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article\ArticleSearchIndex;
use App\Models\Article\ConversionCount;
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
 * @property-read int|null $past_view_count
 * @property-read int|null $past_conversion_count
 * @property-read bool $hasFile
 * @property-read Collection<int, Category> $categoryPaks
 * @property-read bool $hasTrhumbnail
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property \Carbon\CarbonImmutable|null $modified_at
 * @property \Carbon\CarbonImmutable|null $created_at
 *
 * @method static Builder<Article> page()
 * @method static Builder<Article> pak(string $slug)
 *
 * @mixin IdeHelperArticle
 */
class Article extends Model implements Feedable
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
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

    public function routeNotificationForMail(mixed $notification): string
    {
        if (! $this->user || ! $this->user->email) {
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
     * @return MorphMany<Attachment,$this>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    /**
     * @return BelongsToMany<Category,$this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return BelongsToMany<Tag,$this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ViewCount,$this>
     */
    public function viewCounts(): HasMany
    {
        return $this->hasMany(ViewCount::class);
    }

    /**
     * @return HasOne<ViewCount,$this>
     */
    public function totalViewCount(): HasOne
    {
        return $this->hasOne(ViewCount::class)->where('type', ViewCount::TYPE_TOTAL);
    }

    /**
     * @return HasMany<ConversionCount,$this>
     */
    public function conversionCounts(): HasMany
    {
        return $this->hasMany(ConversionCount::class);
    }

    /**
     * @return HasOne<ConversionCount,$this>
     */
    public function totalConversionCount(): HasOne
    {
        return $this->hasOne(ConversionCount::class)->where('type', ConversionCount::TYPE_TOTAL);
    }

    /**
     * この記事から関連付けた記事
     *
     * @return MorphToMany<Article,$this>
     */
    public function articles(): MorphToMany
    {
        return $this->morphToMany(self::class, 'articlable');
    }

    /**
     * この記事が関連付けられた記事
     *
     * @return MorphToMany<Article,$this>
     */
    public function relatedArticles(): MorphToMany
    {
        return $this->morphedByMany(self::class, 'articlable');
    }

    /**
     * @return HasOne<ArticleSearchIndex,$this>
     */
    public function seachIndex(): HasOne
    {
        return $this->hasOne(ArticleSearchIndex::class, 'article_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
     */

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
            'articleUserName' => $this->user?->name ?? 'Unknown',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RSS
    |--------------------------------------------------------------------------
     */
    #[\Override]
    public function toFeedItem(): FeedItem
    {
        /** @var \Carbon\CarbonImmutable|null $modifiedAt */
        $modifiedAt = $this->modified_at;

        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->contents->getDescription(),
            'updated' => $modifiedAt?->toMutable(), // CarbonImmutableは未対応
            'link' => route('articles.show', ['userIdOrNickname' => $this->user->nickname ?? $this->user_id, 'articleSlug' => $this->slug]),
            'authorName' => $this->user->name ?? '',
        ]);
    }

    public function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function withUserTrashed(Builder $builder): void
    {
        $builder->withoutGlobalScope('WithoutTrashedUser');
    }

    /**
     * @param  Builder<Article>  $builder
     */
    protected function scopeUser(Builder $builder, User $user): void
    {
        $builder->where('user_id', $user->id);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function latest(Builder $builder): void
    {
        $builder->latest('published_at');
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $builder): void
    {
        $builder->where('status', ArticleStatus::Publish);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function addon(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::AddonPost, ArticlePostType::AddonIntroduction]);
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function page(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown]);
    }

    /**
     * @param  Builder<Article>  $builder
     *
     * @implements Builder<Article>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function pak(Builder $builder, string $slug): void
    {
        $builder->whereHas('categories', function ($query) use ($slug): void {
            $query->pak()->slug($slug);
        });
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function announce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereHas('categories', function (Builder $query): void {
                $query->page()->slug('announce');
            });
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function withoutAnnounce(Builder $builder): void
    {
        $builder->whereIn('post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereDoesntHave('categories', function (Builder $query): void {
                $query->page()->slug('announce');
            });
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function category(Builder $builder, Category $category): void
    {
        $builder->join('article_category', function (JoinClause $joinClause) use ($category): void {
            $joinClause->on('article_category.article_id', '=', 'articles.id')
                ->where('article_category.category_id', $category->id);
        });
    }

    /**
     * @param  Builder<Article>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function pakAddonCategory(Builder $builder, Category $pak, Category $addon): void
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
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function tag(Builder $builder, Tag $tag): void
    {
        $builder->join('article_tag', function (JoinClause $joinClause) use ($tag): void {
            $joinClause->on('article_tag.article_id', '=', 'articles.id')
                ->where('article_tag.tag_id', $tag->id);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isAddonPost(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): bool => ($this->attributes['post_type'] ?? null) === ArticlePostType::AddonPost->value
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isPublish(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): bool => ($this->attributes['status'] ?? null) === ArticleStatus::Publish->value
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isReservation(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): bool => ($this->attributes['status'] ?? null) === ArticleStatus::Reservation->value
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function isInactive(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): bool => in_array($this->attributes['status'] ?? null, [
                ArticleStatus::Draft->value,
                ArticleStatus::Private->value,
                ArticleStatus::Trash->value,
            ])
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function hasThumbnail(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): bool => ! is_null($this->contents->thumbnail) && $this->thumbnail);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<Attachment|null, never>
     */
    protected function thumbnail(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            /** @var object{thumbnail?: int|null} $contents */
            $contents = $this->contents;
            $id = $contents->thumbnail ?? null;

            return $this->attachments->first(fn ($attachment): bool => (string) $id == $attachment->id);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function thumbnailUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => ($this->has_thumbnail && $this->thumbnail) ? $this->thumbnail->thumbnail : $this->getPublicDisk()->url(DefaultThumbnail::NO_THUMBNAIL));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function hasFile(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): bool => $this->is_addon_post
            && $this->contents instanceof AddonPostContent
            && ! is_null($this->contents->file) && $this->file);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<Attachment|null, never>
     */
    protected function file(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if ($this->contents instanceof AddonPostContent) {
                $id = $this->contents->file;

                return $this->attachments->first(fn ($attachment): bool => (string) $id == $attachment->id);
            }

            throw new Exception('invalid post type');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<bool, never>
     */
    protected function hasFileInfo(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): bool => $this->hasFile && $this->file && $this->file->fileInfo);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<Collection<int, Category>, never>
     */
    protected function categoryPaks(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->categories->filter(fn ($category): bool => $category->type === CategoryType::Pak));
    }

    #[\Override]
    protected static function booted(): void
    {
        // 論理削除されていないユーザーを持つ
        self::addGlobalScope('WithoutTrashedUser', function (Builder $builder): void {
            $builder->has('user');
        });
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'contents' => ToArticleContents::class,
            'status' => ArticleStatus::class,
            'post_type' => ArticlePostType::class,
            'published_at' => 'immutable_datetime',
            'modified_at' => 'immutable_datetime',
            'pr' => 'boolean',
        ];
    }
}
