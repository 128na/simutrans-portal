<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    /**
     * リクエストからレスポンスの間にDBトランザクションを適用する
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @link https://gist.github.com/rodrigopedra/a4a91948bd41617a9b1a
     */
    public function handle($request, Closure $next)
    {
        return DB::transaction(fn () => $next($request), 5);
    }
}
