<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Slugable;
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
    */
    protected $attributes = [
        'contents' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'contents',
        'status',
    ];
    protected $casts = [
        'contents' => 'array',
    ];

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
    public function user()
    {
        return $this->belongsTo(User::class);
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
    public function scopeLatest($query)
    {
        return $query->active()->orderBy('updated_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function getDescriptionAttribute()
    {
        return $this->contents['description'] ?? '';
    }
    public function getLinkAttribute()
    {
        return $this->contents['link'] ?? '';
    }
    public function getAuthorAttribute()
    {
        return $this->contents['author'] ?? '';
    }
    public function getLicenseAttribute()
    {
        return $this->contents['license'] ?? '';
    }
    public function getThanksAttribute()
    {
        return $this->contents['thanks'] ?? '';
    }
    public function getThumbnailAttribute()
    {
        $id = $this->contents['thumbnail'] ?? '';
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }
    public function getThumbnailUrlAttribute()
    {
        return asset('uploads/'.$this->thumbnail->path);
    }
    public function getFileAttribute()
    {
        $id = $this->contents['file'] ?? '';
        return $this->attachments->first(function($attachment) use ($id) {
            return $id === $attachment->id;
        });
    }
    public function getFileUrlAttribute()
    {
        return asset('uploads/'.$this->file->path);
    }
    public function getPostTypeAttribute()
    {
        return $this->categories->first(function($category) {
            return $category->has_parent && $category->parent->slug === 'post-type';
        });
    }
    public function getIsPostAttribute()
    {
        return $this->post_type->slug === 'addon-post';
    }
    public function getIsIntroductionAttribute()
    {
        return $this->post_type->slug === 'addon-introduction';
    }
    public function getTypesAttribute()
    {
        return $this->categories->filter(function($category) {
            return $category->has_parent && $category->parent->slug === 'type';
        });
    }
    public function getIsPublishAttribute()
    {
        return $this->status === config('status.publish');
    }

    /*
    |--------------------------------------------------------------------------
    | 一般
    |--------------------------------------------------------------------------
    */
}
