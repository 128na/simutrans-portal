<?php

namespace App\Models\User;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
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
        'memo',
        'order',
    ];

    public const BOOKMARK_ITEMABLE_TYPES = [
        Article::class,
        Bookmark::class,
        Category::class,
        Tag::class,
        User::class,
    ];

    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }

    /**
     * 指定モデル：Bookmark,User,Article,Category,Tag.
     */
    public function bookmarkItemable(): MorphTo
    {
        return $this->morphTo();
    }
}
