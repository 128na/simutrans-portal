<?php
namespace App\Override;

/**
 * クエリをキャッシュする
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
            $sql_str = str_replace('?', '"%s"', $this->toSql());
            $sql_str = vsprintf($sql_str, $this->getBindings());
            $key = 'query:' . hash('sha256', $sql_str);

            return \Cache::remember(
                $key,
                config('app.cache_lifetime_min', 60),
                fn () => parent::get($columns)
            );
        } else {
            return parent::get($columns);
        }
    }
}
