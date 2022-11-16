<?php

namespace App\Http\Middleware;

use App\Models\ControllOption;
use Closure;

class RestrictControl
{
    public function __construct(private ControllOption $controllOption)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next, string $type)
    {
        $this->handleRestrict($type);

        return $next($request);
    }

    private function handleRestrict(string $type)
    {
        switch ($type) {
            case 'login':
                return abort_if($this->controllOption->restrictLogin(), 403);
            case 'register':
                return abort_if($this->controllOption->restrictRegister(), 403);
            case 'update_article':
                return abort_if($this->controllOption->restrictArticleUpdate(), 403);
            case 'update_tag':
                return abort_if($this->controllOption->restrictTagUpdate(), 403);
            case 'invitation_code':
                return abort_if($this->controllOption->restrictInvitationCode(), 403);
        }
    }
}
