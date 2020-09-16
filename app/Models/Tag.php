<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * 記事にリレーションがない孤独なタグを削除する
     */
    public static function removeDoesntHaveRelation()
    {
        self::doesntHave('articles')->delete();
    }
}
