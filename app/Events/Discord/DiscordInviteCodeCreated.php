<?php

declare(strict_types=1);

namespace App\Events\Discord;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscordInviteCodeCreated
{
    use Dispatchable;
    use SerializesModels;
}
