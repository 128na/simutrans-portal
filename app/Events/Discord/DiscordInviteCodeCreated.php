<?php

declare(strict_types=1);

namespace App\Events\Discord;

use Illuminate\Queue\SerializesModels;

class DiscordInviteCodeCreated
{
    use SerializesModels;
}
