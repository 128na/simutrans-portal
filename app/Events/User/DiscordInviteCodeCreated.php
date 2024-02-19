<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class DiscordInviteCodeCreated
{
    use SerializesModels;

    public function __construct(public readonly User $user)
    {
    }
}
