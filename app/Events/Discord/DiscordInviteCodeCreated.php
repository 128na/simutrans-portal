<?php

declare(strict_types=1);

namespace App\Events\Discord;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class DiscordInviteCodeCreated
{
    use Dispatchable;
    use SerializesModels;
}
