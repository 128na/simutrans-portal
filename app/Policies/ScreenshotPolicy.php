<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ScreenshotStatus;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScreenshotPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function showPublic(?User $user, Screenshot $screenshot): bool
    {
        return $screenshot->status === ScreenshotStatus::Publish;
    }

    public function update(User $user, Screenshot $screenshot): bool
    {
        return $user->id === $screenshot->user_id;
    }

    public function destroy(User $user, Screenshot $screenshot): bool
    {
        return $user->id === $screenshot->user_id;
    }
}
