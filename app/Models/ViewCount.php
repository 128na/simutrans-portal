<?php

namespace App\Models;

use App\Models\Article;
use App\Traits\CountUpable;
use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    use CountUpable;

    protected $fillable = [
        'article_id',
        'type',
        'period',
        'count',
    ];

    public $timestamps = false;

    public static function getTableName()
    {
        return 'view_counts';
    }

    protected $casts = [
        'count' => 'integer',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
