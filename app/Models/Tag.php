<?php

namespace App\Models;

use App\Models\User\BookmarkItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function bookmarkItemables(): MorphMany
    {
        return $this->morphMany(BookmarkItem::class, 'bookmark_itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopePopular($query)
    {
        return $query->withCount(['articles' => fn ($query) => $query->active()])
            ->orderBy('articles_count', 'desc');
    }
}
