<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $name タグ名
 * @property string|null $description 説明
 * @property bool $editable 1:編集可,0:編集不可
 * @property int|null $created_by
 * @property int|null $last_modified_by
 * @property CarbonImmutable|null $last_modified_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Article> $articles
 * @property-read int|null $articles_count
 * @property-read User|null $createdBy
 * @property-read User|null $lastModifiedBy
 *
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperTag
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'editable',
        'created_by',
        'last_modified_by',
        'last_modified_at',
    ];

    /**
     * @return BelongsToMany<Article,$this,Pivot>
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * @return BelongsTo<User,$this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User,$this>
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
    #[\Override]
    protected function casts(): array
    {
        return [
            'editable' => 'boolean',
            'last_modified_at' => 'datetime',
        ];
    }
}
