<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 
 *
 * @property int $id
 * @property string $name タグ名
 * @property string|null $description 説明
 * @property bool $editable 1:編集可,0:編集不可
 * @property int|null $created_by
 * @property int|null $last_modified_by
 * @property \Carbon\CarbonImmutable|null $last_modified_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastModifiedBy
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag popular()
 * @method static Builder|Tag query()
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'editable',
        'created_by',
        'last_modified_by',
        'last_modified_at',
    ];

    protected $casts = [
        'editable' => 'boolean',
        'last_modified_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany<Article>
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * @return BelongsTo<User,Tag>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User,Tag>
     */
    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    /**
     * @return array{tagId:int,tagName:string}
     */
    public function getInfoLogging(): array
    {
        return [
            'tagId' => $this->id,
            'tagName' => $this->name,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    /**
     * @param  Builder<Tag>  $builder
     */
    public function scopePopular(Builder $builder): void
    {
        $builder->withCount(['articles' => fn ($q) => $q->active()])
            ->orderBy('articles_count', 'desc');
    }
}
