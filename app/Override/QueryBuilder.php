<?php

namespace App\Override;

use Throwable;

/**
 * クエリをキャッシュする.
 */
class QueryBuilder extends \Illuminate\Database\Query\Builder
{
    private bool $with_cache = false;

    public function withCache()
    {
        $this->with_cache = true;

        return $this;
    }

    public function get($columns = ['*'])
    {
        if ($this->with_cache) {
            $sql = str_replace('?', '"%s"', $this->toSql());
            try {
                $sql = @vsprintf($sql, $this->getBindings());
            } catch (Throwable $e) {
                // 詳細検索だと稀によく引数が合わないっぽい
            }
            if ($sql) {
                logger($sql);
                $key = 'query:'.hash('sha256', $sql);

                return \Cache::remember(
                    $key,
                    config('app.cache_lifetime_min', 60),
                    fn () => parent::get($columns)
                );
            }
        }

        return parent::get($columns);
    }
}
