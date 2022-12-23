<?php

namespace App\Models;

use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    }

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePost($query)
    {
        return $query->type(config('category.type.post'));
    }

    public function scopePak($query)
    {
        return $query->type(config('category.type.pak'));
    }

    public function scopeAddon($query)
    {
        return $query->type(config('category.type.addon'));
    }

    public function scopePak128Position($query)
    {
        return $query->type(config('category.type.pak128_position'));
    }

    public function scopeLicense($query)
    {
        return $query->type(config('category.type.license'));
    }

    public function scopePage($query)
    {
        return $query->type(config('category.type.page'));
    }

    public function scopeForUser($query, User $user)
    {
        if (! $user->isAdmin()) {
            $query->where('need_admin', 0);
        }
    }
}
