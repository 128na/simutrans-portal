<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class BasePolicy
{
    use HandlesAuthorization;

    protected function isSameUser(User $user, Model $model): bool
    {
        return $user->id === $model->user_id;
    }
}
