<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Redirect;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RedirectPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Redirect $redirect): bool
    {
        return $this->isSameUser($user, $redirect);
    }
}
