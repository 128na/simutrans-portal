<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
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
    /** @use HasFactory<\Database\Factories\TagFactory> */
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
     * @return BelongsToMany<Article,$this,\Illuminate\Database\Eloquent\Relations\Pivot>
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
