<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Services\LogMappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Log\LoggerInterface;

class LoggingController extends Controller
{
    public function __construct(
        private LoggerInterface $logger,
        private LogMappingService $logMappingService,
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->logMappingService->mapping($request->all());
        $user = Auth::check() ? 'useeId:'.Auth::id() : 'guest';
        $this->logger->error($user, $data);
    }
}
