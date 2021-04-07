<?php

namespace App\Models;

use App\Models\User\BookmarkItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function bookmarkItemables(): MorphToMany
    {
        return $this->morphToMany(BookmarkItem::class, 'bookmark_itemable');
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
