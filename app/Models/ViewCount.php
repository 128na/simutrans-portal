<?php

namespace App\Models;

use App\Models\Article;
use App\Traits\Countable;
use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    use Countable;

    protected $fillable = [
        'article_id',
        'type',
        'period',
        'count',
    ];

    public $timestamps = false;

    protected $casts = [
        'count' => 'integer',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
