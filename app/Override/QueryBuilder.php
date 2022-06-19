<?php

namespace App\Override;

use Illuminate\Database\Query\Builder;
use Throwable;

/**
 * クエリをキャッシュする.
 */
class QueryBuilder extends Builder
{
    private bool $withCache = false;

    public function withCache()
    {
        $this->withCache = true;

        return $this;
    }

    public function get($columns = ['*'])
    {
        if ($this->withCache) {
            $sql = str_replace('?', '"%s"', $this->toSql());
            try {
                $sql = @vsprintf($sql, $this->getBindings());
                if ($sql) {
                    $key = 'query:'.hash('sha256', $sql);

                    return \Cache::remember(
                        $key,
                        config('app.cache_lifetime_min', 60),
                        fn () => parent::get($columns)
                    );
                }
            } catch (Throwable $e) {
                // 詳細検索だと稀によく引数が合わないっぽい
            }
        }

        return parent::get($columns);
    }
}
