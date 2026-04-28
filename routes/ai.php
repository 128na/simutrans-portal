<?php

declare(strict_types=1);

use App\Mcp\Servers\SimutransAddonPortalGuestServer;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', SimutransAddonPortalGuestServer::class);

Mcp::web('/mcp/user', SimutransAddonPortalUserServer::class)
    ->middleware('auth:sanctum');
