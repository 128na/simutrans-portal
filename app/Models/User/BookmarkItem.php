<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookmarkItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bookmark_id',
        'bookmark_itemable_type',
        'bookmark_itemable_id',
        'user_id',
        'memo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }

    /**
     * 指定モデル：Bookmark,User,Article,Category,Tag.
     */
    public function bookmarkItemables(): MorphTo
    {
        return $this->morphTo();
    }
}
