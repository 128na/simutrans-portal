<?php

namespace App\Console\Commands;

use App\Services\TwitterAnalytics\PKCEService;
use Illuminate\Console\Command;

class Oauth2Authorize extends Command
{
    protected $signature = 'oauth2:authorize';

    protected $description = 'authorize';

    public function __construct(
        private PKCEService $pkceService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $state = $this->pkceService->generateState();
        $codeVerifier = $this->pkceService->generateCodeVerifier();
        $codeChallange = $this->pkceService->generateCodeChallenge($codeVerifier);

        $authUrl = $this->pkceService->generateAuthorizeUrl($state, $codeChallange);
        $this->info('you need authorize: '.$authUrl);

        $code = $this->pkceService->getCode($state);

        $this->info('code:'.$code);

        $data = $this->pkceService->generateToken($code, $codeVerifier);

        logger('auth', $data);
    }
}
