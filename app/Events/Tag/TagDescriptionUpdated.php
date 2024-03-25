<?php

declare(strict_types=1);

namespace App\Events\Tag;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TagDescriptionUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Tag $tag, public readonly User $user, public readonly ?string $old = null)
    {
    }
}
