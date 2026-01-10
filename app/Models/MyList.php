<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $note
 * @property bool $is_public
 * @property string|null $slug
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, MyListItem> $items
 *
 * @method static Builder<MyList> whereBelongsToUser(User|int $user)
 * @method static Builder<MyList> wherePublic()
 *
 * @mixin IdeHelperMyList
 */
class MyList extends Model
{
    /** @use HasFactory<\Database\Factories\MyListFactory> */
    use HasFactory;

    protected $table = 'mylists';

    protected $fillable = [
        'user_id',
        'title',
        'note',
        'is_public',
        'slug',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /**
     * リストの所有者（ユーザー）を取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リストのアイテムを取得
     */
    public function items(): HasMany
    {
        return $this->hasMany(MyListItem::class, 'list_id');
    }

    /**
     * 特定ユーザーのリストをフィルタ
     */
    public function scopeWhereBelongsToUser(Builder $query, int|User $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $userId);
    }

    /**
     * 公開リストのみをフィルタ
     */
    public function scopeWherePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }
}
