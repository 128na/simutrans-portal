<?php

namespace App\Models;
use App\Models\Article;
use App\Traits\Slugable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use Slugable;

    protected $fillable = [
        'name',
        'type',
        'slug',
        'order',
    ];

    /*
    |--------------------------------------------------------------------------
    | グローバルスコープ
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
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

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
}
