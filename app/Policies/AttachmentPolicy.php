<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        return true;
    }

    public function update(User $user, Attachment $attachment): bool
    {
        return $this->isSameUser($user, $attachment);
    }
}
