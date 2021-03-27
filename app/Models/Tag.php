<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopePopular($query)
    {
        return $query->withCount('articles')->orderBy('articles_count', 'desc');
    }

    /**
     * 記事にリレーションがない孤独なタグを削除する.
     */
    public static function removeDoesntHaveRelation()
    {
        self::doesntHave('articles')->delete();
    }

    /**
     * 記事に関連づいていないタグを削除する.
     */
    public static function deleteUnrelated(): int
    {
        return self::leftJoin('article_tag', 'tags.id', '=', 'article_tag.tag_id')
            ->whereNull('article_id')
            ->delete();
    }
}
