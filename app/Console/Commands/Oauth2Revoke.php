<?php

namespace App\Console\Commands;

use App\Services\TwitterAnalytics\PKCEService;
use Illuminate\Console\Command;

class Oauth2Revoke extends Command
{
    protected $signature = 'oauth2:revoke';

    protected $description = 'revoke';

    public function __construct(
        private PKCEService $pkceService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $token = $this->pkceService->getAccessToken();

        $this->pkceService->revokeToken($token);
    }
}
