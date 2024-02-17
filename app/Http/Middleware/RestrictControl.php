<?php

declare(strict_types=1);

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
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, ?string $type = null)
    {
        $this->handleRestrict($type ?? $request->route()->uri);

        return $next($request);
    }

    private function handleRestrict(string $type): void
    {
        match ($type) {
            'login' => abort_if($this->controllOption->restrictLogin(), 403),
            'register' => abort_if($this->controllOption->restrictRegister(), 403),
            'update_article' => abort_if($this->controllOption->restrictArticleUpdate(), 403),
            'update_tag' => abort_if($this->controllOption->restrictTagUpdate(), 403),
            'invitation_code' => abort_if($this->controllOption->restrictInvitationCode(), 403),
            default => null,
        };
    }
}
