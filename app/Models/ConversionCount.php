<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionCount extends Model
{
    public const TYPE_DAILY = 1;
    public const TYPE_MONTHLY = 2;
    public const TYPE_YEARLY = 3;
    public const TYPE_TOTAL = 4;

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
