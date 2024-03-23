<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Http\Request;

class BaseListener
{
    /**
     * @return array<mixed>
     */
    protected function getAccessInfo(?Request $request = null): array
    {
        $request ??= request();

        return [
            'REMOTE_ADDR' => $request->server('REMOTE_ADDR', 'N/A'),
            'HTTP_REFERER' => $request->server('HTTP_REFERER', 'N/A'),
            'HTTP_USER_AGENT' => $request->server('HTTP_USER_AGENT', 'N/A'),
        ];
    }
}
