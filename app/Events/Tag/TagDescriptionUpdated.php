<?php

declare(strict_types=1);

namespace App\Events\Tag;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class TagDescriptionUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Tag $tag,
        public User $user,
        public null|string $old = null,
    ) {}
}
