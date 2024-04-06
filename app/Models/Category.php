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
 * 
 *
 * @property int $id
 * @property CategoryType $type 分類
 * @property string $slug スラッグ
 * @property bool $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @method static Builder|Category addon()
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static Builder|Category forUser(\App\Models\User $user)
 * @method static Builder|Category license()
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category page()
 * @method static Builder|Category pak()
 * @method static Builder|Category pak128Position()
 * @method static Builder|Category query()
 * @method static Builder|Category slug(string $slug)
 * @method static Builder|Category type(\App\Enums\CategoryType $categoryType)
 * @mixin \Eloquent
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
