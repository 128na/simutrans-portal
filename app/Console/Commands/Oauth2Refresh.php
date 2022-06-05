<?php

namespace App\Console\Commands;

use App\Services\TwitterAnalytics\PKCEService;
use Illuminate\Console\Command;

class Oauth2Refresh extends Command
{
    protected $signature = 'oauth2:refresh';

    protected $description = 'refresh';

    public function __construct(
        private PKCEService $pkceService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $token = $this->pkceService->getRefreshToken();

        $this->pkceService->refreshToken($token);
    }
}
