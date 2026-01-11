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
 * @property-read \App\Models\Article $article
 * @property-read \App\Models\MyList $list
 *
 * @method static \Database\Factories\MyListItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|MyListItem newModelQuery()
 * @method static Builder<static>|MyListItem newQuery()
 * @method static Builder<static>|MyListItem orderByPosition()
 * @method static Builder<static>|MyListItem query()
 * @method static Builder<static>|MyListItem whereListId(int $listId)
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Article, self> article()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\MyList, self> list()
 *
 * @mixin \Eloquent
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
     *
     * @return BelongsTo<MyList, self>
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(MyList::class, 'list_id');
    }

    /**
     * このアイテムが参照する記事を取得
     *
     * @return BelongsTo<Article, self>
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 特定リストのアイテムをフィルタ
     *
     * @param  Builder<MyListItem>  $query
     * @return Builder<MyListItem>
     */
    public function scopeWhereListId(Builder $query, int $listId): Builder
    {
        return $query->where('list_id', $listId);
    }

    /**
     * 位置順にソート
     *
     * @param  Builder<MyListItem>  $query
     * @return Builder<MyListItem>
     */
    public function scopeOrderByPosition(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('created_at');
    }
}
