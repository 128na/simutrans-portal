<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryType;
use App\Traits\Slugable;
use Carbon\CarbonImmutable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property CategoryType $type 分類
 * @property string $slug スラッグ
 * @property bool $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Article> $articles
 * @property-read int|null $articles_count
 *
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Category forUser(\App\Models\User $user)
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category order()
 * @method static Builder<static>|Category page()
 * @method static Builder<static>|Category pak()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category slug(string $slug)
 * @method static Builder<static>|Category type(\App\Enums\CategoryType $categoryType)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
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

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
     */
    /**
     * @return BelongsToMany<Article,$this>
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
    #[Scope]
    protected function order(Builder $builder): void
    {
        $builder->orderBy('order', 'asc');
    }

    /**
     * @param  Builder<Category>  $builder
     */
    #[Scope]
    protected function type(Builder $builder, CategoryType $categoryType): void
    {
        $builder->where('type', $categoryType);
    }

    /**
     * @param  Builder|Category  $builder
     */
    #[Scope]
    protected function pak(Builder $builder): void
    {
        $builder->type(CategoryType::Pak);
    }

    /**
     * @param  Builder|Category  $builder
     */
    #[Scope]
    protected function page(Builder $builder): void
    {
        $builder->type(CategoryType::Page);
    }

    /**
     * @param  Builder|Category  $builder
     */
    #[Scope]
    protected function forUser(Builder $builder, User $user): void
    {
        if (! $user->isAdmin()) {
            $builder->where('need_admin', 0);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 初期化時設定
    |--------------------------------------------------------------------------
     */
    #[\Override]
    protected static function boot(): void
    {
        parent::boot();
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
            'need_admin' => 'boolean',
        ];
    }
}
