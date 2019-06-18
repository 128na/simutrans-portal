<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

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
        \DB::beginTransaction();
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
        if ($response instanceof Response && $response->getStatusCode() > 399) {
            \DB::rollBack();
        } else {
            \DB::commit();
        }
        return $response;
    }
}
