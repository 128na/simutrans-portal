<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use Slugable;

    protected $fillable = [
        'type',
        'slug',
        'order',
        'need_admin',
    ];

    protected $casts = [
        'type' => CategoryType::class,
        'need_admin' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder): void {
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
    public function scopeType(Builder $builder, CategoryType $type): void
    {
        $builder->where('type', $type);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePak(Builder $builder): void
    {
        $builder->type(CategoryType::Pak);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopeAddon(Builder $builder): void
    {
        $builder->type(CategoryType::Addon);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePak128Position(Builder $builder): void
    {
        $builder->type(CategoryType::Pak128Position);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopeLicense(Builder $builder): void
    {
        $builder->type(CategoryType::License);
    }

    /**
     * @param  Builder|Category  $builder
     */
    public function scopePage(Builder $builder): void
    {
        $builder->type(CategoryType::Page);
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
