<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\ViewCount;
use App\Models\UserAddonCount;
use App\Models\PakAddonCount;
use App\Models\ConversionCount;
use App\Traits\Slugable;
use App\Traits\JsonFieldable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
    use Slugable;
    use JsonFieldable;

    /*
        アドオン紹介
            contents = {
                description: 説明文
                author: 作者名
                link: リンク先URL
                thumbnail?: サムネイル画像ID
                thanks?: 元アドオン、謝辞
                license?: ライセンス
            };
        アドオン投稿
            contents = {
                description: 説明文
                author: 作者名
                file: 添付ファイルID
                thumbnail?: サムネイル画像ID
                thanks?: 元アドオン、謝辞
                license?: ライセンス
            };
        一般記事
            contents = [
                {type:text content:文章},
                {type:image id:添付画像ID},
                ...
            ];
    */
    protected $attributes = [
        'contents' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'post_type',
        'contents',
        'status',
    ];
    protected $casts = [
        'contents' => 'array',
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

        self::created(function($model) {
            $model->syncRelatedData();
        });
        self::updated(function($model) {
            $model->syncRelatedData();
        });
        self::deleted(function($model) {
            $model->syncRelatedData();
        });
    }
    private function syncRelatedData()
    {
        UserAddonCount::recount();
        PakAddonCount::recount();
        Tag::removeDoesntHaveRelation();
    }

    public function getJsonableField()
    {
        return 'contents';
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

        return $query->where('title', 'like', $like_word)
            ->orWhere('contents->description', 'like', $like_word)
            ->orWhere('contents->thanks', 'like', $like_word)
            ->orWhere('contents->author', 'like', $like_word)
            ->orWhere('contents->license', 'like', $like_word);
    }
    public function scopeAddon($query)
    {
        $query->whereIn('post_type', ['addon-post', 'addon-introduction']);
    }
    public function scopePage($query)
    {
        $query->where('post_type', 'page');
    }
    public function scopeAnnounce($query)
    {
        $query->where('post_type', 'page')
            ->whereHas('categories', function($query) {
                $query->where('type', 'page')->where('slug', 'announce');
            });
    }
    public function scopeWithoutAnnounce($query)
    {
        $query->where('post_type', 'page')
            ->whereDoesntHave('categories', function($query) {
                $query->where('type', 'page')->where('slug', 'announce');
            });
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
    public function getDescriptionAttribute()
    {
        return $this->getContents('description');
    }
    public function getLinkAttribute()
    {
        return $this->getContents('link');
    }
    public function getAuthorAttribute()
    {
        return $this->getContents('author');
    }
    public function getLicenseAttribute()
    {
        return $this->getContents('license');
    }
    public function getThanksAttribute()
    {
        return $this->getContents('thanks');
    }
    public function getAgreementAttribute()
    {
        return $this->getContents('agreement');;
    }

    public function getThumbnailAttribute()
    {
        $id = $this->getContents('thumbnail');
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }
    public function getFileAttribute()
    {
        $id = $this->getContents('file');
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }

    public function getHasThumbnailAttribute()
    {
        return !!$this->thumbnail;
    }
    public function getThumbnailUrlAttribute()
    {
        return $this->has_thumbnail
             ? asset('storage/'.$this->thumbnail->path)
             : asset('storage/'.config('attachment.no-thumbnail'));
    }
    public function getThumbnailIdAttribute()
    {
        return $this->thumbnail->id ?? null;
    }
    public function getHasFileAttribute()
    {
        return !is_null($this->file);
    }

    public function getCategoryPaksAttribute()
    {
        return $this->categories->filter(function($category) {
            return $category->type === config('category.type.pak');
        });
    }
    public function getCategoryAddonsAttribute()
    {
        return $this->categories->filter(function($category) {
            return $category->type === config('category.type.addon');
        });
    }
    public function getCategoryPak128PositionsAttribute()
    {
        return $this->categories->filter(function($category) {
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
        return $this->getContents('description', null)
            ?? collect($this->getContents('sections', []))->first(function($s) { return $s['type'] === 'text';})['text']
            ?? $this->title;
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */
    public function isAnnounce()
    {
        return $this->categories->search(function($category) {
            return $category->type === 'page' && $category->slug === 'announce';
        }) !== false;
    }
    public function hasCategory($id)
    {
        return $this->categories->search(function($category) use($id) {
            return $category->id === $id;
        }) !== false;
    }
    public function getImage($id)
    {
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }
    public function getImageUrl($id)
    {
        $image = $this->getImage($id);
        return $image
             ? asset('storage/'.$image->path)
             : asset('storage/'.config('attachment.no-thumbnail'));
    }
}
