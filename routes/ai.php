<?php

declare(strict_types=1);

use App\Mcp\Servers\SimutransAddonPortalGuestServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', SimutransAddonPortalGuestServer::class);
