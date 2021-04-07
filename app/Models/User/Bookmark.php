<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'is_public',
        'title',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkItems(): HasMany
    {
        return $this->hasMany(BookmarkItem::class);
    }

    public function bookmarkItemables(): MorphToMany
    {
        return $this->morphToMany(BookmarkItem::class, 'bookmark_itemable');
    }
}
