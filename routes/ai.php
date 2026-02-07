<?php

declare(strict_types=1);

use App\Mcp\Servers\SimutransAddonPortalGuestServer;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::oauthRoutes();

Mcp::web('/mcp', SimutransAddonPortalGuestServer::class);

Mcp::web('/mcp-auth', SimutransAddonPortalUserServer::class)
    ->middleware('auth:mcp');
