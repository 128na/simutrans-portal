<?php

namespace App\Models;

use App\Models\Article;
use App\Models\PakAddonCount;
use App\Models\UserAddonCount;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Slugable;

    protected $fillable = [
        'name',
        'type',
        'slug',
        'order',
        'need_admin',
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
            $builder->orderBy('order', 'asc');
        });

        self::created(function ($model) {
            $model->recountHandler();
        });
        self::updated(function ($model) {
            $model->recountHandler();
        });
        self::deleted(function ($model) {
            $model->recountHandler();
        });
    }
    private function recountHandler()
    {
        UserAddonCount::recount();
        PakAddonCount::recount();
    }

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    public function scopePost($query)
    {
        return $query->where('type', config('category.type.post'));
    }
    public function scopePak($query)
    {
        return $query->where('type', config('category.type.pak'));
    }
    public function scopeAddon($query)
    {
        return $query->where('type', config('category.type.addon'));
    }
    public function scopePak128Position($query)
    {
        return $query->where('type', config('category.type.pak128_position'));
    }
    public function scopeLicense($query)
    {
        return $query->where('type', config('category.type.license'));
    }
    public function scopePage($query)
    {
        return $query->where('type', config('category.type.page'));
    }
    public function scopeForUser($query, User $user)
    {
        if (!$user->isAdmin()) {
            $query->where('need_admin', 0);
        }
    }

}
