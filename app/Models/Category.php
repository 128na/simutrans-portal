<?php

declare(strict_types=1);

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

        static::addGlobalScope('order', static function (Builder $builder): void {
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
    public function scopeType(Builder $builder, string $type): void
    {
        $builder->where('type', $type);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePost(Builder $builder): void
    {
        $builder->type(config('category.type.post'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePak(Builder $builder): void
    {
        $builder->type(config('category.type.pak'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopeAddon(Builder $builder): void
    {
        $builder->type(config('category.type.addon'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePak128Position(Builder $builder): void
    {
        $builder->type(config('category.type.pak128_position'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopeLicense(Builder $builder): void
    {
        $builder->type(config('category.type.license'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePage(Builder $builder): void
    {
        $builder->type(config('category.type.page'));
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopeForUser(Builder $builder, User $user): void
    {
        if (! $user->isAdmin()) {
            $builder->where('need_admin', 0);
        }
    }
}
