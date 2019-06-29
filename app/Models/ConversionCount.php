<?php

namespace App\Models;

use App\Models\Article;
use App\Traits\CountUpable;
use Illuminate\Database\Eloquent\Model;

class ConversionCount extends Model
{
    use CountUpable;

    protected $fillable = [
        'article_id',
        'type',
        'period',
        'count',
    ];

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
