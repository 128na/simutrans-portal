<?php
namespace App\Traits;


trait Countable
{
    public static $types = [
        'daily'   => 1,
        'monthly' => 2,
        'yearly'  => 3,
        'total'   => 4,
    ];

    public static function countUp($article_id)
    {
        $now = now();

        $this_day   = $now->format('Ymd');
        $this_month = $now->format('Ym');
        $this_year  = $now->format('Y');
        $total      = 'total';

        self::countUpBy($article_id, self::$types['daily'], $this_day);
        self::countUpBy($article_id, self::$types['monthly'], $this_month);
        self::countUpBy($article_id, self::$types['yearly'], $this_year);
        self::countUpBy($article_id, self::$types['total'], $total);
    }

    private static function countUpBy($article_id, $type, $period)
    {
        $countable = static::firstOrNew([
            'type' => $type,
            'period' => $period,
            'article_id' => $article_id
        ]);
        $countable->fill([
            'count' => $countable->count ? $countable->count + 1 : 1
        ])->save();
    }
}
