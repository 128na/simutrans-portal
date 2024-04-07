<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    use HasFactory;

    /**
     * @use Slugable<Category>
     */
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
    /**
     * @return BelongsToMany<Article>
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
    /**
     * @param  Builder<Category>  $builder
     */
    public function scopeType(Builder $builder, CategoryType $categoryType): void
    {
        $builder->where('type', $categoryType);
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
