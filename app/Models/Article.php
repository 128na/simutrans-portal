<?php

namespace App\Models;

use App\Casts\ToArticleContents;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Model implements Feedable
{
    use Slugable;
    use Notifiable;

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

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
        self::created(function ($model) {
            $model->syncRelatedData();
        });
        self::updated(function ($model) {
            $model->syncRelatedData();
        });
        self::deleted(function ($model) {
            $model->syncRelatedData();
        });
    }
    public function syncRelatedData()
    {
        UserAddonCount::recount();
        PakAddonCount::recount();
        Cache::tags(['response'])->flush();
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
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function viewCounts()
    {
        return $this->hasMany(ViewCount::class);
    }
    public function todaysViewCount()
    {
        return $this->hasOne(ViewCount::class)
            ->where('type', ViewCount::$types['daily'])
            ->where('period', now()->format('Ymd'));
    }

    public function dailyViewCounts()
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::$types['daily']);
    }
    public function monthlyViewCounts()
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::$types['monthly']);
    }
    public function yearlyViewCounts()
    {
        return $this->hasMany(ViewCount::class)->where('type', ViewCount::$types['yearly']);
    }
    public function totalViewCount()
    {
        return $this->hasOne(ViewCount::class)->where('type', ViewCount::$types['total']);
    }

    public function conversionCounts()
    {
        return $this->hasMany(ConversionCount::class);
    }
    public function todaysConversionCount()
    {
        return $this->hasOne(ConversionCount::class)
            ->where('type', ConversionCount::$types['daily'])
            ->where('period', now()->format('Ymd'));
    }
    public function dailyConversionCounts()
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::$types['daily']);
    }
    public function monthlyConversionCounts()
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::$types['monthly']);
    }
    public function yearlyConversionCounts()
    {
        return $this->hasMany(ConversionCount::class)->where('type', ConversionCount::$types['yearly']);
    }
    public function totalConversionCount()
    {
        return $this->hasOne(ConversionCount::class)->where('type', ConversionCount::$types['total']);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
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
            ->where(function ($query) {
                $query->whereNull('contents->exclude_link_check');
                $query->orWhere('contents->exclude_link_check', false);
            });
    }
    public function scopePage($query)
    {
        $query->where('post_type', 'page');
    }
    public function scopePak($query, $slug)
    {
        $query->whereHas('categories', function ($query) use ($slug) {
            $query->pak()->slug($slug);
        });
    }

    public function scopeAnnounce($query)
    {
        $query->whereIn('post_type', ['page', 'markdown'])
            ->whereHas('categories', function ($query) {
                $query->page()->slug('announce');
            });
    }
    public function scopeWithoutAnnounce($query)
    {
        $query->whereIn('post_type', ['page', 'markdown'])
            ->whereDoesntHave('categories', function ($query) {
                $query->page()->slug('announce');
            });
    }
    /**
     * ランキング
     * 閲覧数が当日、当月、当年、合計の多⇒少順
     */
    public function scopeRanking($query)
    {
        $datetime = now();

        $query->select('articles.*'); // view_countのフィールドがあるとリレーションデータが取れない（多分idが複数あるから？）
        $query->leftJoin('view_counts as d', function ($join) use ($datetime) {
            $join->on('d.article_id', 'articles.id')
                ->where('d.type', 1)
                ->where('d.period', $datetime->format('Ymd'));
        });
        $query->leftJoin('view_counts as m', function ($join) use ($datetime) {
            $join->on('m.article_id', 'articles.id')
                ->where('m.type', 1)
                ->where('m.period', $datetime->format('Ym'));
        });
        $query->leftJoin('view_counts as y', function ($join) use ($datetime) {
            $join->on('y.article_id', 'articles.id')
                ->where('y.type', 1)
                ->where('y.period', $datetime->format('Y'));
        });
        $query->leftJoin('view_counts as t', function ($join) {
            $join->on('t.article_id', 'articles.id')
                ->where('t.type', 1)
                ->where('t.period', 'total');
        });
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
        return $this->attachments->first(function ($attachment) use ($id) {
            return (string) $id == $attachment->id;
        });
    }
    public function getThumbnailUrlAttribute()
    {
        return $this->has_thumbnail
        ? asset('storage/' . $this->thumbnail->path)
        : asset('storage/' . config('attachment.no-thumbnail'));
    }

    public function getHasFileAttribute()
    {
        return !is_null($this->contents->file) && $this->file;
    }
    public function getFileAttribute()
    {
        $id = $this->contents->file;
        return $this->attachments->first(function ($attachment) use ($id) {
            return (string) $id == $attachment->id;
        });
    }

    public function getCategoryPaksAttribute()
    {
        return $this->categories->filter(function ($category) {
            return $category->type === config('category.type.pak');
        });
    }
    public function getCategoryAddonsAttribute()
    {
        return $this->categories->filter(function ($category) {
            return $category->type === config('category.type.addon');
        });
    }
    public function getCategoryPak128PositionsAttribute()
    {
        return $this->categories->filter(function ($category) {
            return $category->type === config('category.type.pak128_position');
        });
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
        return $this->content->description ?? collect($this->content->sections ?? [])->first(function ($s) {return $s['type'] === 'text';})['text'] ?? $this->title;
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
        return $this->categories->search(function ($category) {
            return $category->type === 'page' && $category->slug === 'announce';
        }) !== false;
    }
    public function hasCategory($id)
    {
        return $this->categories->search(function ($category) use ($id) {
            return $category->id === $id;
        }) !== false;
    }
    public function getImage($id)
    {
        return $this->attachments->first(function ($attachment) use ($id) {
            return (string) $id == $attachment->id;
        });
    }
    public function getImageUrl($id)
    {
        $image = $this->getImage($id);
        return $image
        ? asset('storage/' . $image->path)
        : asset('storage/' . config('attachment.no-thumbnail'));
    }

    /*
    |--------------------------------------------------------------------------
    | RSS
    |--------------------------------------------------------------------------
     */
    public function toFeedItem()
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
