<?php

namespace App\Models;

use App\Casts\ToArticleContents;
use App\Models\User\BookmarkItem;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Model implements Feedable
{
    use Notifiable;
    use HasFactory;
    use SoftDeletes;
    use Slugable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
    ];
    protected $casts = [
        'contents' => ToArticleContents::class,
    ];

    protected static function booted()
    {
        // 論理削除されていないユーザーを持つ
        static::addGlobalScope('WithoutTrashedUser', function (Builder $builder) {
            $builder->has('user');
        });
    }

    public function routeNotificationForMail($notification)
    {
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

    public function bookmarkItemables(): MorphMany
    {
        return $this->morphMany(BookmarkItem::class, 'bookmark_itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    public function scopeWithUserTrashed($query)
    {
        $query->withoutGlobalScope('WithoutTrashedUser');
    }

    public function scopeUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', config('status.publish'));
    }

    public function scopeWithForList($query)
    {
        return $query->with('user', 'attachments', 'categories', 'tags');
    }

    public function scopeSearch($query, $word)
    {
        $word = trim($word);
        $like_word = "%{$word}%";

        return $query->where('title', 'LIKE', $like_word)
            ->orWhere('contents', 'LIKE', $like_word);
    }

    public function scopeAddon($query)
    {
        $query->whereIn('post_type', ['addon-post', 'addon-introduction']);
    }

    public function scopeLinkCheckTarget($query)
    {
        $query->where('post_type', 'addon-introduction')
            ->where(
                fn ($query) => $query->whereNull('contents->exclude_link_check')
                    ->orWhere('contents->exclude_link_check', false)
            );
    }

    public function scopePage($query)
    {
        $query->where('post_type', 'page');
    }

    public function scopePak($query, $slug)
    {
        $query->whereHas('categories', fn ($query) => $query->pak()->slug($slug));
    }

    public function scopeAnnounce($query)
    {
        $query->whereIn('post_type', ['page', 'markdown'])
            ->whereHas('categories', fn ($query) => $query->page()->slug('announce'));
    }

    public function scopeWithoutAnnounce($query)
    {
        $query->whereIn('post_type', ['page', 'markdown'])
            ->whereDoesntHave('categories', fn ($query) => $query->page()->slug('announce'));
    }

    /**
     * ランキング
     * 閲覧数が当日、当月、当年、合計の多⇒少順.
     */
    public function scopeRanking($query)
    {
        $datetime = now();

        $query->select('articles.*'); // view_countのフィールドがあるとリレーションデータが取れない（多分idが複数あるから？）
        $query->leftJoin(
            'view_counts as d',
            fn ($join) => $join
                ->on('d.article_id', 'articles.id')
                ->where('d.type', 1)
                ->where('d.period', $datetime->format('Ymd'))
        );
        $query->leftJoin(
            'view_counts as m',
            fn ($join) => $join
                ->on('m.article_id', 'articles.id')
                ->where('m.type', 1)
                ->where('m.period', $datetime->format('Ym'))
        );
        $query->leftJoin(
            'view_counts as y',
            fn ($join) => $join
                ->on('y.article_id', 'articles.id')
                ->where('y.type', 1)
                ->where('y.period', $datetime->format('Y'))
        );
        $query->leftJoin(
            'view_counts as t',
            fn ($join) => $join->on('t.article_id', 'articles.id')
                ->where('t.type', 1)
                ->where('t.period', 'total')
        );
        $query->orderBy('d.count', 'desc');
        $query->orderBy('m.count', 'desc');
        $query->orderBy('y.count', 'desc');
        $query->orderBy('t.count', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
     */
    public function getIsPublishAttribute()
    {
        return $this->status === config('status.publish');
    }

    public function getHasThumbnailAttribute()
    {
        return !is_null($this->contents->thumbnail) && $this->thumbnail;
    }

    public function getThumbnailAttribute()
    {
        $id = $this->contents->thumbnail;

        return $this->attachments->first(fn ($attachment) => (string) $id == $attachment->id);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->has_thumbnail
        ? asset('storage/'.$this->thumbnail->path)
        : asset('storage/'.config('attachment.no-thumbnail'));
    }

    public function getHasFileAttribute()
    {
        return !is_null($this->contents->file) && $this->file;
    }

    public function getFileAttribute()
    {
        $id = $this->contents->file;

        return $this->attachments->first(fn ($attachment) => (string) $id == $attachment->id);
    }

    public function getCategoryPaksAttribute()
    {
        return $this->categories->filter(fn ($category) => $category->type === config('category.type.pak'));
    }

    public function getCategoryAddonsAttribute()
    {
        return $this->categories->filter(fn ($category) => $category->type === config('category.type.addon'));
    }

    public function getCategoryPak128PositionsAttribute()
    {
        return $this->categories->filter(fn ($category) => $category->type === config('category.type.pak128_position'));
    }

    public function getTodaysConversionRateAttribute()
    {
        if (!is_null($this->todaysConversionCount) && $this->todaysViewCount) {
            $rate = $this->todaysConversionCount->count / $this->todaysViewCount->count * 100;

            return sprintf('%.1f %%', $rate);
        }

        return 'N/A';
    }

    public function getMetaDescriptionAttribute()
    {
        return mb_strimwidth($this->contents->getDescription(), 0, 300, '…');
    }

    public function getHeadlineDescriptionAttribute()
    {
        return mb_strimwidth($this->contents->getDescription(), 0, 55, '…');
    }

    public function getUrlDecodedSlugAttribute()
    {
        return urldecode($this->slug);
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
     */
    public function isAnnounce()
    {
        return $this->categories->some(fn ($category) => $category->type === 'page' && $category->slug === 'announce');
    }

    public function hasCategory($id)
    {
        return $this->categories->some(fn ($category) => $category->id === $id);
    }

    public function getImage($id)
    {
        return $this->attachments->first(
            fn ($attachment) => (string) $id == $attachment->id
        );
    }

    public function getImageUrl($id)
    {
        $image = $this->getImage($id);

        return $image
        ? asset('storage/'.$image->path)
        : asset('storage/'.config('attachment.no-thumbnail'));
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
            'summary' => $this->contents->getDescription() ?? '',
            'updated' => $this->updated_at->toMutable(), // CarbonImmutableは未対応
            'link' => route('articles.show', $this->slug),
            'author' => $this->user->name,
        ]);
    }
}
