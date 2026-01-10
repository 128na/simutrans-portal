<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $list_id
 * @property int $article_id
 * @property string|null $note
 * @property int|null $position
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read MyList $list
 * @property-read Article $article
 *
 * @method static Builder<MyListItem> whereListId(int $listId)
 * @method static Builder<MyListItem> orderByPosition()
 *
 * @mixin IdeHelperMyListItem
 */
class MyListItem extends Model
{
    /** @use HasFactory<\Database\Factories\MyListItemFactory> */
    use HasFactory;

    protected $table = 'mylist_items';

    protected $fillable = [
        'list_id',
        'article_id',
        'note',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /**
     * このアイテムが属するリストを取得
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(MyList::class, 'list_id');
    }

    /**
     * このアイテムが参照する記事を取得
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 特定リストのアイテムをフィルタ
     */
    public function scopeWhereListId(Builder $query, int $listId): Builder
    {
        return $query->where('list_id', $listId);
    }

    /**
     * 位置順にソート
     */
    public function scopeOrderByPosition(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('created_at');
    }
}
