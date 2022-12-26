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
    public function scopeType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function scopePost(Builder $query, Category $model): void
    {
        $model->type(config('category.type.post'));
    }

    public function scopePak(Builder $query, Category $model): void
    {
        $model->type(config('category.type.pak'));
    }

    public function scopeAddon(Builder $query, Category $model): void
    {
        $model->type(config('category.type.addon'));
    }

    public function scopePak128Position(Builder $query, Category $model): void
    {
        $model->type(config('category.type.pak128_position'));
    }

    public function scopeLicense(Builder $query, Category $model): void
    {
        $model->type(config('category.type.license'));
    }

    public function scopePage(Builder $query, Category $model): void
    {
        $model->type(config('category.type.page'));
    }

    public function scopeForUser(Builder $query, User $user): void
    {
        if (! $user->isAdmin()) {
            $query->where('need_admin', 0);
        }
    }
}
